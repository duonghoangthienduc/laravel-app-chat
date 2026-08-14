import {avatarStyle, getInitials} from '../utils/avatar.js';
import {getCsrfTokenFromCookie} from '../echo.js';

export default function chatInbox(userId, initialConversationId = null) {
	return {
		userId,
		conversations: [],
		activeId: initialConversationId,
		search: '',
		messages: [],
		loadingMessages: false,
		draft: '',
		otherTyping: false,
		typingTimeout: null,
		typingWhisperTimer: null,

		getInitials,
		avatarStyle,

		get filteredConversations() {
			if (!this.search) {
				return this.conversations;
			}
			return this.conversations.filter(c =>
				c.other_name?.toLowerCase().includes(this.search.toLowerCase())
			);
		},

		get groupedMessages() {
			const groups = [];
			for (const msg of this.messages) {
				const lastGroup = groups[groups.length - 1];
				if (lastGroup && lastGroup.sender_id === msg.sender_id) {
					lastGroup.items.push(msg);
				}
				else {
					groups.push({
						sender_id: msg.sender_id,
						sender_name: msg.sender_name,
						items: [msg]
					});
				}
			}
			return groups;
		},

		get activeConversation() {
			return this.conversations.find(c => c.id === this.activeId) ?? null;
		},

		closeConversation() {
			if (this.activeId) {
				window.Echo.leave(`conversation.${this.activeId}`);
			}
			this.activeId = null;
		},

		subscribeToConversation(id) {
			window.Echo.private(`conversation.${id}`)
				.listen('.message.sent', (message) => {
					if (message.sender_id !== this.userId) {
						if (!message.sender_name) {
							message.sender_name = this.activeConversation?.other_name;
						}
						this.otherTyping = false; // tin nhắn thật đã tới, ẩn
						                          // typing indicator ngay
						this.messages.push(message);
						this.$nextTick(() => this.scrollToBottom());
					}
				})
				.listenForWhisper('typing', (e) => {
					if (e.user_id === this.userId) {
						return;
					} // bỏ qua whisper của chính mình

					this.otherTyping = true;
					this.$nextTick(() => this.scrollToBottom());

					clearTimeout(this.typingTimeout);
					this.typingTimeout = setTimeout(() => {
						this.otherTyping = false;
					}, 3000); // tự ẩn nếu 3s không có whisper mới (người kia
					          // dừng gõ)
				});
		},

		// Gọi từ @input trên ô nhập — throttle để không spam whisper mỗi ký tự
		notifyTyping() {
			if (!this.activeId) {
				return;
			}
			if (this.typingWhisperTimer) {
				return;
			} // đang trong khoảng chờ, bỏ qua

			window.Echo.private(`conversation.${this.activeId}`).whisper('typing', {
				user_id: this.userId,
			});

			this.typingWhisperTimer = setTimeout(() => {
				this.typingWhisperTimer = null;
			}, 2000); // tối đa 1 whisper mỗi 2s
		},

		async init() {
			await fetch('/sanctum/csrf-cookie', {credentials: 'include'});

			const res = await fetch('/api/v1/chat/conversations', {credentials: 'include'});
			const json = await res.json();
			this.conversations = json.data;

			if (this.activeId) {
				await this.loadMessages(this.activeId);
				this.subscribeToConversation(this.activeId);
			}
		},

		async selectConversation(id) {
			if (this.activeId) {
				window.Echo.leave(`conversation.${this.activeId}`);
			}

			this.activeId = id;
			this.otherTyping = false;
			await this.loadMessages(id);
			this.subscribeToConversation(id);
		},

		async loadMessages(conversationId) {
			this.loadingMessages = true;
			this.messages = [];

			const res = await fetch(`/api/v1/chat/conversations/${conversationId}/messages`, {
				credentials: 'include',
			});
			const data = await res.json();
			this.messages = data.data.reverse();
			this.loadingMessages = false;

			this.$nextTick(() => this.scrollToBottom());
		},

		async send() {
			if (!this.draft.trim() || !this.activeId) {
				return;
			}

			const content = this.draft;
			this.draft = '';

			// Optimistic: hiện tin nhắn ngay, không chờ server
			const tempId = `temp-${Date.now()}`;
			this.messages.push({
				id: tempId,
				sender_id: this.userId,
				sender_name: null,
				content,
				created_at: null,
				_pending: true,
				_failed: false,
			});
			this.$nextTick(() => this.scrollToBottom());

			try {
				const res = await fetch(`/api/v1/chat/conversations/${this.activeId}/messages`, {
					method: 'POST',
					credentials: 'include',
					headers: {
						'Content-Type': 'application/json',
						'X-XSRF-TOKEN': getCsrfTokenFromCookie(),
					},
					body: JSON.stringify({content}),
				});

				if (!res.ok) {
					throw new Error('send failed');
				}

				const saved = await res.json();
				const index = this.messages.findIndex(m => m.id === tempId);
				if (index !== -1) {
					this.messages.splice(index, 1, saved.data);
				}
			}
			catch (e) {
				const index = this.messages.findIndex(m => m.id === tempId);
				if (index !== -1) {
					this.messages[index]._pending = false;
					this.messages[index]._failed = true;
				}
			}

			this.$nextTick(() => this.scrollToBottom());
		},

		scrollToBottom() {
			this.$refs.scrollBox.scrollTop = this.$refs.scrollBox.scrollHeight;
		},
	};
}