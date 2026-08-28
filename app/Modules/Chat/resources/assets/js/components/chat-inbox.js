import {avatarStyle, getInitials} from '../utils/avatar.js';
import {getCsrfTokenFromCookie, getEcho} from "@/services/echo.js";
import {formatDayLabel} from '../utils/time.js';

export default function chatInbox(userId, initialConversationId = null, mediaEnabled = false, initialConversation = null, initialMessages = [], initialNextCursor = null, messagesPreloaded = false) {
	const echo = getEcho();

	return {
		userId,
		mediaEnabled,
		conversations: initialConversation ? [initialConversation] : [],
		activeId: initialConversationId,
		search: '',
		messages: initialMessages,
		loadingMessages: false,
		draft: '',
		otherTyping: false,
		typingTimeout: null,
		typingWhisperTimer: null,

		connectionState: 'connecting',

		isNearBottom: true,
		newMessageCount: 0,

		nextCursor: initialNextCursor,
		messagesPreloaded,
		loadingOlder: false,

		pendingMedia: [],
		isSendingMedia: false,

		getInitials,
		avatarStyle,

		get filteredConversations() {

			if (!this.search) {
				return this.conversations;
			}

			return this.conversations.filter(c => c.other_name?.toLowerCase().includes(this.search.toLowerCase()));
		},

		get activeConversation() {
			return this.conversations.find(c => c.id === this.activeId) ?? null;
		},

		closeConversation() {

			if (this.activeId) {
				echo.leave(`conversation.${this.activeId}`);
			}
			this.activeId = null;

			if (this.resizeObserver) {
				this.resizeObserver.disconnect();
			}
		},

		get groupedByDay() {
			const days = [];

			for (const msg of this.messages) {
				const label = formatDayLabel(msg.created_at_iso);
				let lastDay = days[days.length - 1];

				if (!lastDay || lastDay.label !== label) {
					lastDay = {label, groups: []};
					days.push(lastDay);
				}

				const lastGroup = lastDay.groups[lastDay.groups.length - 1];

				if (lastGroup && lastGroup.sender_id === msg.sender_id) {
					lastGroup.items.push(msg);
				}
				else {
					lastDay.groups.push({
						sender_id: msg.sender_id,
						sender_name: msg.sender_name,
						items: [msg]
					});
				}
			}
			return days;
		},

		onScroll() {
			const el = this.$refs.scrollBox;

			if (!el) {
				return;
			}

			const bottomThreshold = 150;
			this.isNearBottom = el.scrollHeight - el.scrollTop - el.clientHeight < bottomThreshold;

			if (this.isNearBottom) {
				this.newMessageCount = 0;
			}

			const topThreshold = 80;

			if (el.scrollTop < topThreshold && this.nextCursor && !this.loadingOlder) {
				this.loadOlderMessages();
			}
		},

		async loadOlderMessages() {

			if (!this.nextCursor || this.loadingOlder) {
				return;
			}

			this.loadingOlder = true;
			const el = this.$refs.scrollBox;
			const prevScrollHeight = el.scrollHeight;
			const prevScrollTop = el.scrollTop;

			const res = await fetch(`/api/v1/chat/conversations/${this.activeId}/messages?cursor=${this.nextCursor}`, {credentials: 'include'});
			const data = await res.json();
			const older = data.data.reverse();

			this.messages = [...older, ...this.messages];
			this.nextCursor = data.meta?.next_cursor ?? null;
			this.loadingOlder = false;

			this.$nextTick(() => {
				el.style.scrollBehavior = 'auto';
				el.scrollTop = el.scrollHeight - prevScrollHeight + prevScrollTop;
				requestAnimationFrame(() => {
					el.style.scrollBehavior = 'smooth';
				});

				this.fillViewportIfNeeded();
			});
		},

		jumpToNewest() {
			this.newMessageCount = 0;
			this.scrollToBottom();
		},

		subscribeToConversation(id) {
			echo.private(`conversation.${id}`)
				.listen('.message.sent', (message) => {

					if (message.sender_id !== this.userId) {

						if (!message.sender_name) {
							message.sender_name = this.activeConversation?.other_name;
						}
						this.otherTyping = false;
						this.messages.push(message);

						if (this.isNearBottom) {
							this.$nextTick(() => this.scrollToBottom());
						}
						else {
							this.newMessageCount++;
						}
					}
				})
				.listenForWhisper('typing', (e) => {

					if (e.user_id === this.userId) {
						return;
					}

					this.otherTyping = true;

					if (this.isNearBottom) {
						this.$nextTick(() => this.scrollToBottom());
					}

					clearTimeout(this.typingTimeout);
					this.typingTimeout = setTimeout(() => {
						this.otherTyping = false;
					}, 3000);
				});
		},

		notifyTyping() {

			if (!this.activeId || this.typingWhisperTimer) {
				return;
			}

			echo.private(`conversation.${this.activeId}`).whisper('typing', {
				user_id: this.userId,
			});

			this.typingWhisperTimer = setTimeout(() => {
				this.typingWhisperTimer = null;
			}, 2000);
		},

		initConnectionState() {
			const conn = echo?.connector?.pusher?.connection;

			if (!conn) {
				return;
			}

			this.connectionState = conn.state;
			conn.bind('state_change', (states) => {
				this.connectionState = states.current;
			});
		},

		setupResizeObserver() {
			const el = this.$refs.scrollBox;

			if (!el) {
				return;
			}

			let timer;
			this.resizeObserver = new ResizeObserver(() => {
				clearTimeout(timer);
				timer = setTimeout(() => {
					if (this.isNearBottom) {
						this.scrollToBottom();
					}
				}, 80);
			});
			this.resizeObserver.observe(el);
		},

		async init() {

			if (this.messagesPreloaded) {
				this.$nextTick(() => this.scrollToBottom(true));
			}

			await fetch('/sanctum/csrf-cookie', {credentials: 'include'});
			this.initConnectionState();

			const res = await fetch('/api/v1/chat/conversations', {credentials: 'include'});
			const json = await res.json();

			const byId = new Map(this.conversations.map(c => [c.id, c]));

			for (const c of json.data) {
				byId.set(c.id, c);
			}
			this.conversations = Array.from(byId.values());

			if (this.activeId) {
				await this.ensureConversationLoaded(this.activeId);

				if (this.messagesPreloaded) {
					this.$nextTick(() => this.fillViewportIfNeeded());
				}
				else {
					await this.loadMessages(this.activeId);
				}

				this.subscribeToConversation(this.activeId);
			}

			this.$nextTick(() => this.setupResizeObserver());
		},

		async ensureConversationLoaded(id) {
			if (this.conversations.some(c => c.id === id)) {
				return;
			}

			const res = await fetch(`/api/v1/chat/conversations/${id}`, {credentials: 'include'});
			if (!res.ok) {
				return;
			}
			const json = await res.json();
			this.conversations.unshift(json.data);
		},

		async selectConversation(id) {
			if (this.activeId) {
				echo.leave(`conversation.${this.activeId}`);
			}

			this.activeId = id;
			this.otherTyping = false;
			await this.loadMessages(id);
			this.subscribeToConversation(id);
		},

		async loadMessages(conversationId) {
			this.loadingMessages = true;
			this.messages = [];
			this.isNearBottom = true;
			this.newMessageCount = 0;
			this.nextCursor = null;

			const res = await fetch(`/api/v1/chat/conversations/${conversationId}/messages`, {
				credentials: 'include',
			});
			const data = await res.json();
			this.messages = data.data.reverse();
			this.nextCursor = data.meta?.next_cursor ?? null;
			this.loadingMessages = false;

			this.$nextTick(() => {
				this.scrollToBottom(true);
				this.fillViewportIfNeeded();
			});
		},

		async send() {
			if ((!this.draft.trim() && this.pendingMedia.length === 0) || !this.activeId) {
				return;
			}

			if (this.isSendingMedia) {
				return;
			}

			const content = this.draft;
			const filesToUpload = [...this.pendingMedia];
			this.draft = '';
			this.newMessageCount = 0;

			const tempId = `temp-${Date.now()}`;
			this.messages.push({
				id: tempId,
				sender_id: this.userId,
				sender_name: null,
				content,
				media: filesToUpload.map(f => ({id: f.id, url: f.url})),
				created_at: null,
				created_at_iso: new Date().toISOString(),
				_pending: true,
				_failed: false,
			});
			this.pendingMedia = [];
			this.$nextTick(() => this.scrollToBottom());

			try {
				let mediaIds = [];

				if (filesToUpload.length > 0) {
					this.isSendingMedia = true;
					// Upload tuần tự — tránh spam server nếu user dán nhiều
					// ảnh cùng lúc.
					const uploaded = [];
					for (const item of filesToUpload) {
						uploaded.push(await this.uploadFile(item.file));
						URL.revokeObjectURL(item.url);
					}
					mediaIds = uploaded.map(m => m.id);
					this.isSendingMedia = false;
				}

				const res = await fetch(`/api/v1/chat/conversations/${this.activeId}/messages`, {
					method: 'POST', credentials: 'include', headers: {
						'Content-Type': 'application/json',
						'X-XSRF-TOKEN': getCsrfTokenFromCookie(),
					}, body: JSON.stringify({content, media_ids: mediaIds}),
				});

				if (!res.ok) {
					throw new Error('send failed');
				}

				const saved = await res.json();
				const index = this.messages.findIndex((m) => m.id === tempId);
				if (index !== -1) {
					this.messages.splice(index, 1, saved.data);
				}
			}
			catch (e) {
				this.isSendingMedia = false;
				const index = this.messages.findIndex((m) => m.id === tempId);
				if (index !== -1) {
					this.messages[index]._pending = false;
					this.messages[index]._failed = true;
				}
			}

			this.$nextTick(() => this.scrollToBottom());
		},

		scrollToBottom(instant = false) {
			const el = this.$refs.scrollBox;
			if (!el) {
				return;
			}
			el.scrollTo({
				top: el.scrollHeight, behavior: instant ? 'auto' : 'smooth',
			});
		},

		fillViewportIfNeeded() {
			const el = this.$refs.scrollBox;
			if (!el || !this.nextCursor || this.loadingOlder) {
				return;
			}

			if (el.scrollHeight <= el.clientHeight) {
				this.loadOlderMessages();
			}
		},

		autoGrow() {
			const el = this.$refs.draftInput;
			if (!el) {
				return;
			}
			el.style.height = 'auto';
			el.style.height = Math.min(el.scrollHeight, 200) + 'px';
		},

		handleEnter(e) {
			if (e.shiftKey) {
				return; // để mặc định trình duyệt tự xuống dòng
			}
			e.preventDefault();
			this.send();
		},

		handlePaste(event) {
			if (!this.mediaEnabled) {
				return;
			}

			const items = event.clipboardData?.items;

			if (!items) {
				return;
			}

			const imageItems = Array.from(items).filter(item => item.type.startsWith('image/'));

			if (imageItems.length === 0) {
				return;
			}

			event.preventDefault();

			for (const item of imageItems) {
				const file = item.getAsFile();

				if (file) {
					this.addPendingFile(file);
				}
			}
		},

		openFilePicker() {
			if (!this.mediaEnabled) {
				return;
			}
			this.$refs.fileInput.click();
		},

		handleFileSelect(event) {
			const files = Array.from(event.target.files || []);
			for (const file of files) {
				this.addPendingFile(file);
			}
			event.target.value = '';
		},

		addPendingFile(file) {
			this.pendingMedia.push({
				id: `local-${Date.now()}-${Math.random()}`,
				file,
				url: URL.createObjectURL(file),
			});
		},

		removePendingMedia(id) {
			const item = this.pendingMedia.find(m => m.id === id);

			if (item) {
				URL.revokeObjectURL(item.url);
			}
			this.pendingMedia = this.pendingMedia.filter(m => m.id !== id);
		},

		async uploadFile(file) {
			const formData = new FormData();
			formData.append('file', file);
			formData.append('context', 'chat');

			const res = await fetch('/api/v1/media', {
				method: 'POST',
				credentials: 'include',
				headers: {'X-XSRF-TOKEN': getCsrfTokenFromCookie()},
				body: formData,
			});

			if (!res.ok) {
				throw new Error('upload failed');
			}

			const json = await res.json();
			return json.data;
		},
	};
}