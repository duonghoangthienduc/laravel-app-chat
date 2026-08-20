import {getEcho} from "@/services/echo.js";

export function initOnlinePresence() {
	const echo = getEcho();

	window.Alpine.store('onlinePresence', {
		userIds: new Set(),
		isOnline(id) {
			return this.userIds.has(Number(id));
		},
	});

	echo.join('online-users')
		.here((users) => {
			window.Alpine.store('onlinePresence').userIds = new Set(users.map(u => u.id));
		})
		.joining((user) => {
			window.Alpine.store('onlinePresence').userIds.add(user.id);
		})
		.leaving((user) => {
			window.Alpine.store('onlinePresence').userIds.delete(user.id);
		})
		.error((error) => {
			console.error('Presence channel error:', error);
		});
}