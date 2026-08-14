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
			});
		},

		handleMessageSent(payload) {
			const index = this.conversations.findIndex(c => c.id === payload.conversation_id);

			if (index === -1) {
				return;
			}

			const conv = {...this.conversations[index]};
			conv.last_message = payload.content;
			conv.last_message_at = payload.created_at_iso;

			this.conversations.splice(index, 1);
			this.conversations.unshift(conv);
		},
	};
}