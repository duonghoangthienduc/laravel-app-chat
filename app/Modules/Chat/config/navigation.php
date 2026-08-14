<?php

use Modules\Chat\Support\ConversationNavResolver;

return [
	[
		'heading'  => 'Chat',
		'priority' => 10,
		'items'    => [
			[
				'label'     => 'New Chat',
				'icon'      => 'chat-bubble-left-right',
				'route'     => 'chat',
				'active_on' => ['chat'],
			],
			[
				'label'             => 'Inbox',
				'icon'              => 'inbox',
				'route'             => 'chat.inbox',
				'active_on'         => ['chat.inbox', 'chat.show'],
				'children_view'     => 'chat::layouts.partials.nav-children',
				'children_resolver' => ConversationNavResolver::class,
			],
		],
	],
];