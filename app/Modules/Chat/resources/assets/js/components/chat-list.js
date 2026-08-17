import {avatarStyle, getInitials} from '../utils/avatar.js';
import {formatRelativeTime} from '../utils/time.js';

export default function chatList(userId, activeId = null) {
	return {
		userId,
		activeId,
		conversations: [],
		search: '',
		loading: true,
		now: Date.now(),

		// --- Scroll tracking / jump-to-top ---
		isAtTop: true,
		newActivityCount: 0,

		getInitials,
		avatarStyle,

		formatTime(iso) {
			return formatRelativeTime(iso, this.now);
		},

		get filteredConversations() {
			if (!this.search) {
				return this.conversations;
			}
			return this.conversations.filter(c =>
				c.other_name?.toLowerCase().includes(this.search.toLowerCase())
			);
		},

		onScroll() {
			const el = this.$refs.listBox;
			if (!el) {
				return;
			}

			this.isAtTop = el.scrollTop < 40;

			if (this.isAtTop) {
				this.newActivityCount = 0;
			}
		},

		jumpToTop() {
			this.newActivityCount = 0;
			this.$refs.listBox?.scrollTo({top: 0, behavior: 'smooth'});
		},

		async init() {
			await fetch('/sanctum/csrf-cookie', {credentials: 'include'});

			this.loading = true;
			const res = await fetch('/api/v1/chat/conversations', {credentials: 'include'});
			const json = await res.json();
			this.conversations = json.data;
			this.loading = false;

			setInterval(() => {
				this.now = Date.now();
			}, 30_000);

			window.Echo.private(`App.Models.User.${this.userId}`)
				.listen('.conversation.created', (payload) => this.handleConversationCreated(payload))
				.listen('.message.sent', (payload) => this.handleMessageSent(payload));
		},

		handleConversationCreated(payload) {
			if (this.conversations.some(c => c.id === payload.id)) {
				return;
			}

			const other = payload.participants.find(p => p.id !== this.userId);

			this.conversations.unshift({
				id: payload.id,
				is_group: payload.is_group,
				other_name: payload.is_group ? null : other?.name,
				other_user_id: other?.id,
				last_message: payload.last_message,
				last_message_at: payload.last_message_at,
				last_message_sender_id: payload.last_message_sender_id,
			});

			if (!this.isAtTop) {
				this.newActivityCount++;
			}
		},

		handleMessageSent(payload) {
			const index = this.conversations.findIndex(c => c.id === payload.conversation_id);

			if (index === -1) {
				return;
			}

			const conv = {...this.conversations[index]};
			conv.last_message = payload.content;
			conv.last_message_at = payload.created_at_iso;
			conv.last_message_sender_id = payload.sender_id;

			this.conversations.splice(index, 1);
			this.conversations.unshift(conv);

			if (!this.isAtTop) {
				this.newActivityCount++;
			}
		},
	};
}