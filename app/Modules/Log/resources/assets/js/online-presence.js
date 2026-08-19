import {getEcho} from "@/services/echo.js";

export function initOnlinePresence() {
	const echo = getEcho();

	echo.join('online-users')
		.here((users) => {
			console.log('Currently online:', users);

			// Store/update your online users.
		})
		.joining((user) => {
			console.log('User online:', user);

			// Add user to your online-user state.
		})
		.leaving((user) => {
			console.log('User offline:', user);

			// Remove user from your online-user state.
		})
		.error((error) => {
			console.error('Presence channel error:', error);
		});
}