import {avatarSideBarStyle, getInitials} from '../utils/avatar.js';
import {getEcho} from "@/services/echo.js";

export default function chatSidebar(userId, initialConversations = [], visibleLimit = 10) {
	const echo = getEcho();

	return {
		userId,
		conversations: initialConversations,
		open: localStorage.getItem('chat-nav-conversations') !== 'closed',
		showAll: false,
		visibleLimit,

		getInitials,
		avatarSideBarStyle,

		get hasMore() {
			return this.conversations.length > this.visibleLimit;
		},

		get visibleConversations() {
			if (this.showAll || !this.hasMore) {
				return this.conversations;
			}
			return this.conversations.slice(0, this.visibleLimit);
		},

		toggleOpen() {
			this.open = !this.open;
			localStorage.setItem('chat-nav-conversations', this.open ? 'open' : 'closed');
		},

		init() {
			echo.private(`App.Models.User.${this.userId}`)
				.listen('.conversation.created', (payload) => this.handleConversationCreated(payload))
				.listen('.message.sent', (payload) => this.handleMessageSent(payload));
		},

		handleConversationCreated(payload) {
			if (this.conversations.some(c => c.id === payload.id)) {
				return;
			}

			const other = payload.participants.find(p => p.id !== this.userId);
			const name = payload.is_group ? null : other?.name;

			this.conversations.unshift({
				id: payload.id,
				other_user_id: other?.id ?? null,
				label: name,
				href: `/chat/inbox/${payload.id}`,
				active: false,
				initial: getInitials(name),
			});
		},

		handleMessageSent(payload) {
			const index = this.conversations.findIndex(c => c.id === payload.conversation_id);

			if (index === -1) {
				return;
			}

			const conv = this.conversations.splice(index, 1)[0];
			this.conversations.unshift(conv);
		},
	};
}