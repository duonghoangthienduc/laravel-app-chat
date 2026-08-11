/* global Alpine */

import './echo.js';
import {getCsrfTokenFromCookie} from "./echo.js";

function registerChatInbox() {
	Alpine.data('chatInbox', (userId, initialConversationId = null) => ({
		userId,
		conversations: [],
		activeId: initialConversationId,
		search: '',
		messages: [],
		loadingMessages: false,
		draft: '',

		getInitials(name) {
			if (!name) {
				return '';
			}
			const parts = name.trim().split(/\s+/);
			const initials = parts.map(p => p[0]).join('').toUpperCase();
			return initials.length > 1
				? initials[0] + initials[initials.length - 1]
				: initials;
		},

		get filteredConversations() {
			if (!this.search) {
				return this.conversations;
			}
			return this.conversations.filter(c =>
				c.other_name?.toLowerCase().includes(this.search.toLowerCase())
			);
		},

		get activeConversation() {
			return this.conversations.find(c => c.id === this.activeId) ?? null;
		},

		async init() {
			await fetch('/sanctum/csrf-cookie', {credentials: 'include'});

			const res = await fetch('/api/v1/chat/conversations', {credentials: 'include'});
			const json = await res.json();
			this.conversations = json.data;

			if (this.activeId) {
				await this.loadMessages(this.activeId);
			}
		},

		async selectConversation(id) {
			if (this.activeId) {
				window.Echo.leave(`conversation.${this.activeId}`);
			}

			this.activeId = id;
			await this.loadMessages(id);
			window.Echo.private(`conversation.${id}`)
				.listen('.message.sent', (message) => {
					if (message.sender_id !== this.userId) {
						this.messages.push(message);
						this.$nextTick(() => this.scrollToBottom());
					}
				});
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

			const res = await fetch(`/api/v1/chat/conversations/${this.activeId}/messages`, {
				method: 'POST',
				credentials: 'include',
				headers: {
					'Content-Type': 'application/json',
					'X-XSRF-TOKEN': getCsrfTokenFromCookie(),
				},
				body: JSON.stringify({content}),
			});

			const saved = await res.json();
			this.messages.push(saved.data); // ← sửa: unwrap data

			this.$nextTick(() => this.scrollToBottom());
		},

		scrollToBottom() {
			this.$refs.scrollBox.scrollTop = this.$refs.scrollBox.scrollHeight;
		},

		async markAsRead(conv) {
			await fetch(`/api/v1/chat/conversations/${conv.id}/read`, { // ← sửa URL
				method: 'PATCH',
				credentials: 'include', // ← thêm
				headers: {'X-XSRF-TOKEN': getCsrfTokenFromCookie()},
			});
			conv.unread = false;
		},

		async deleteConversation(conv) {
			await fetch(`/api/v1/chat/conversations/${conv.id}`, { // ← sửa URL
				method: 'DELETE',
				credentials: 'include', // ← thêm
				headers: {'X-XSRF-TOKEN': getCsrfTokenFromCookie()}, // ← sửa
			});
			this.conversations = this.conversations.filter(c => c.id !== conv.id);
			if (this.activeId === conv.id) {
				this.activeId = null;
			}
		},

		async blockUser(conv) {
			await fetch(`/api/v1/chat/users/${conv.other_user_id}/block`, { // ← sửa URL
				method: 'POST',
				credentials: 'include', // ← thêm
				headers: {'X-XSRF-TOKEN': getCsrfTokenFromCookie()}, // ← sửa
			});
			this.conversations = this.conversations.filter(c => c.id !== conv.id);
		},
	}));
}

if (window.Alpine) {
	registerChatInbox();
}
else {
	document.addEventListener('alpine:init', registerChatInbox);
}