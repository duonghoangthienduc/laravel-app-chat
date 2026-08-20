import chatSidebar from './components/chat-sidebar.js';
import {initOnlinePresence} from '@status/online-presence.js';

function register() {
	window.Alpine.data('chatSidebar', chatSidebar);
	initOnlinePresence();
}

if (window.Alpine) {
	register();
}
else {
	document.addEventListener('alpine:init', register);
}