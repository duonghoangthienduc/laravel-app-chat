import chatList from './components/chat-list.js';
import chatInbox from './components/chat-inbox.js';
import '../css/app.css';

function registerComponents() {
	window.Alpine.data('chatList', chatList);
	window.Alpine.data('chatInbox', chatInbox);
}

if (window.Alpine) {
	registerComponents();
}
else {
	document.addEventListener('alpine:init', registerComponents, {
		once: true,
	});
}