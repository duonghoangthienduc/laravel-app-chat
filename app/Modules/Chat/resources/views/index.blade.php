<x-layouts::app :title="__('Chat Area')">
    <div x-data="chatInbox({{ auth()->id() }}, {{ $activeConversationId ?? 'null' }})" class="flex h-[calc(100vh-4rem)] overflow-hidden">
        {{-- Cột trái: Conversation list --}}
        <div class="w-full max-w-xs shrink-0 border-e border-zinc-200 dark:border-zinc-700 flex flex-col">
            <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                <flux:heading size="lg">{{ __('Conversation') }}</flux:heading>
                <flux:input x-model="search" icon="magnifying-glass" :placeholder="__('Search here')" class="mt-3"/>
            </div>

            <div class="flex-1 overflow-y-auto">
                <template x-for="conv in filteredConversations" :key="conv.id">
                    <div class="relative flex items-start gap-3 px-4 py-3" :class="activeId === conv.id ? 'bg-blue-600 text-white' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'">
                        <button @click="selectConversation(conv.id)" class="flex flex-1 items-start gap-3 text-left min-w-0">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-zinc-300 text-sm font-medium text-zinc-700 dark:bg-zinc-600 dark:text-zinc-200" x-text="getInitials(conv.other_name)"></div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="truncate font-medium" x-text="conv.other_name"></span>
                                    <span class="shrink-0 text-xs opacity-70" x-text="conv.last_message_by"></span>
                                </div>
                                <p class="truncate text-sm opacity-80" x-text="conv.last_message"></p>
                            </div>
                        </button>
						<?php /*
                        <div x-data="{ open: false }" class="relative shrink-0">
                            <button @click.stop="open = !open" class="p-1 opacity-60 hover:opacity-100">
                                <flux:icon name="ellipsis-vertical" variant="micro"/>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 z-10 mt-1 w-40 rounded-lg bg-blue-600 py-1 text-white shadow-lg" style="display: none;">
                                <button @click.stop="blockUser(conv); open = false" class="flex w-full items-center gap-2 px-3 py-2 text-sm hover:bg-blue-700">
                                    <flux:icon name="no-symbol" variant="micro"/> {{ __('Block') }}
                                </button>
                                <button @click.stop="deleteConversation(conv); open = false" class="flex w-full items-center gap-2 px-3 py-2 text-sm hover:bg-blue-700">
                                    <flux:icon name="trash" variant="micro"/> {{ __('Delete') }}
                                </button>
                                <button @click.stop="markAsRead(conv); open = false" class="flex w-full items-center gap-2 px-3 py-2 text-sm hover:bg-blue-700">
                                    <flux:icon name="envelope-open" variant="micro"/> {{ __('Mark as Read') }}
                                </button>
                            </div>
                        </div>
                        */ ?>
                    </div>
                </template>

                <template x-if="filteredConversations.length === 0">
                    <p class="p-4 text-sm text-zinc-400">{{ __('No conversations yet.') }}</p>
                </template>
            </div>
        </div>

        {{-- Cột giữa: Message thread --}}
        <div class="min-w-0 flex-1 flex flex-col">
            <template x-if="!activeId">
                <div class="flex h-full items-center justify-center text-zinc-400">
                    {{ __('Select a conversation to start chatting') }}
                </div>
            </template>

            <template x-if="activeId">
                <div class="flex h-full flex-col">
                    {{-- Header --}}
                    <div class="flex items-center gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-zinc-300 text-sm font-medium text-zinc-700 dark:bg-zinc-600 dark:text-zinc-200" x-text="getInitials(activeConversation?.other_name)"></div>
                        <div class="min-w-0">
                            <flux:heading size="sm" x-text="activeConversation?.other_name"></flux:heading>
                            <flux:text size="sm" class="text-emerald-500">{{ __('Online') }}</flux:text>
                        </div>

                        <flux:spacer/>

                        <flux:button icon="video-camera" variant="ghost"/>
                        <flux:button icon="phone" variant="ghost"/>
                    </div>

                    {{-- Messages --}}
                    <div x-ref="scrollBox" class="flex-1 space-y-4 overflow-y-auto p-4">
                        <template x-if="loadingMessages">
                            <p class="text-center text-sm text-zinc-400">{{ __('Loading...') }}</p>
                        </template>

                        <template x-for="msg in messages" :key="msg.id">
                            <div class="flex items-end gap-2" :class="msg.sender_id === userId ? 'flex-row-reverse' : ''">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-zinc-300 text-xs font-medium text-zinc-700 dark:bg-zinc-600 dark:text-zinc-200" x-text="getInitials(msg.sender_name)"></div>
                                <div class="max-w-md">
                                    <div class="rounded-2xl px-4 py-2 text-sm" :class="msg.sender_id === userId ? 'bg-blue-600 text-white' : 'bg-zinc-100 dark:bg-zinc-700'" x-text="msg.content"></div>
                                    <p class="mt-1 text-xs text-zinc-400" :class="msg.sender_id === userId ? 'text-right' : ''" x-text="msg.created_at_human"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Input --}}
                    <form @submit.prevent="send()" class="flex items-center gap-2 border-t border-zinc-200 p-3 dark:border-zinc-700">
                        <flux:button icon="plus" variant="ghost" type="button"/>
                        <flux:input x-model="draft" :placeholder="__('Type a message here')" class="flex-1"/>
                        <flux:button icon="paper-airplane" type="submit" variant="primary"/>
                    </form>
                </div>
            </template>
        </div>
    </div>
</x-layouts::app>

{{ module_vite('build-chat', 'resources/assets/js/app.js') }}