import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export function getCsrfTokenFromCookie() {
	const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);

	return match ? decodeURIComponent(match[1]) : null;
}

export function getEcho() {

	if (window.Echo) {
		return window.Echo;
	}

	window.Pusher = Pusher;
	window.Echo = new Echo({
		broadcaster: 'reverb',
		key: import.meta.env.VITE_REVERB_APP_KEY,
		wsHost: import.meta.env.VITE_REVERB_HOST,
		wsPort: import.meta.env.VITE_REVERB_PORT,
		wssPort: import.meta.env.VITE_REVERB_PORT,
		forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
		enabledTransports: ['ws', 'wss'],
		authEndpoint: '/broadcasting/auth',
		auth: {
			headers: {
				'X-XSRF-TOKEN': getCsrfTokenFromCookie(),
			},
		},
	});

	return window.Echo;
}