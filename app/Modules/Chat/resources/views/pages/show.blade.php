<x-chat::layouts.chat :title="__('Chat')">
    <div x-data="chatInbox({{ auth()->id() }}, @js($activeConversationId), @js($initialMedia), @js($activeConversation))" class="flex h-[calc(100vh-4rem)] flex-col overflow-hidden transition-[filter] duration-500" :style="connectionState !== 'connected' ? 'filter:saturate(0.7) brightness(0.95);' : ''">
        @include('chat::partials.header')
        @include('chat::partials.messages')
        @include('chat::partials.composer')
    </div>
</x-chat::layouts.chat>

@assets{{ module_vite('build-chat', 'resources/assets/js/app.js') }}@endassets