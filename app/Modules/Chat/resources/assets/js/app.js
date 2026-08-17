import './echo.js';
import chatList from './components/chat-list.js';
import chatInbox from './components/chat-inbox.js';

function registerComponents() {
	window.Alpine.data('chatList', chatList);
	window.Alpine.data('chatInbox', chatInbox);
}

if (window.Alpine) {
	registerComponents();
}
else {
	document.addEventListener('alpine:init', registerComponents);
}