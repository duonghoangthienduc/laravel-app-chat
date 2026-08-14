/* global Alpine */

import './echo.js';
import chatList from './components/chat-list.js';
import chatInbox from './components/chat-inbox.js';
import chatSidebar from './components/chat-sidebar.js';

function registerComponents() {
	Alpine.data('chatList', chatList);
	Alpine.data('chatInbox', chatInbox);
	Alpine.data('chatSidebar', chatSidebar);
}

if (window.Alpine) {
	registerComponents();
}
else {
	document.addEventListener('alpine:init', registerComponents);
}