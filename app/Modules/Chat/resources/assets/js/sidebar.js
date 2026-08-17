import './echo.js';
import chatSidebar from './components/chat-sidebar.js';

function register() {
	window.Alpine.data('chatSidebar', chatSidebar);
}

if (window.Alpine) {
	register();
}
else {
	document.addEventListener('alpine:init', register);
}