<x-layouts::app :title="__('Chat Area')">
    <div x-data="chatList({{ auth()->id() }}, @js($activeConversationId))" class="flex h-[calc(100vh-4rem)] flex-col overflow-hidden">
        {{-- Header --}}
        <div class="border-b border-zinc-800 py-5">
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-400">
                {{ __('Chat') }}
            </p>
            <flux:heading size="lg" class="text-white">{{ __('Conversation') }}</flux:heading>

            <div class="relative mt-3 max-w-md">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
                </svg>
                <input x-model="search" type="text" placeholder="{{ __('Search here') }}" class="w-full rounded-xl border border-zinc-800 bg-zinc-900/80 py-2.5 pl-9 pr-3 text-sm text-white outline-none placeholder:text-zinc-600 transition focus:border-indigo-500/60 focus:ring-4 focus:ring-indigo-500/10"/>
            </div>
        </div>

        <div style="position:relative; flex:1; min-height:0; display:flex; flex-direction:column;">
            <div x-ref="listBox" @scroll="onScroll()" class="flex-1 overflow-y-auto">
                <template x-if="loading">
                    <div class="flex h-full flex-col items-center justify-center gap-4">
                        <svg width="40" height="40" class="animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-sm text-zinc-500">{{ __('Loading conversations...') }}</p>
                    </div>
                </template>

                <template x-if="!loading">
                    <div class="divide-y divide-white/5 border-t border-white/5">
                        <template x-for="conv in filteredConversations" :key="conv.id">
                            <a x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" :href="`/chat/inbox/${conv.id}`" wire:navigate class="group relative flex w-full items-center gap-4 px-4 py-3.5 text-left" style="transition: background-color .15s ease;" :style="conv.id === activeId
                            ? 'background: rgba(99,102,241,.1);' : (hovered ? 'background: #27272a;' : 'background: transparent;')">
                                <span class="absolute left-0 top-1/2 h-8 w-[3px] -translate-y-1/2 rounded-r-full bg-indigo-400 transition-all duration-200" :class="conv.id === activeId ? 'opacity-100' : 'opacity-0'"></span>
                                <div class="flex shrink-0 items-center justify-center rounded-full text-[15px] font-semibold text-white shadow-lg shadow-black/20 ring-2 ring-white/10" style="width:52px;height:52px;" :style="avatarStyle(conv.other_name)" x-text="getInitials(conv.other_name)"></div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <span class="truncate text-[15px] font-semibold text-white" x-text="conv.other_name"></span>
                                        <span class="shrink-0 text-[11px] text-zinc-500" x-text="formatTime(conv.last_message_at)"></span>
                                    </div>
                                    <p class="mt-1 truncate text-sm text-zinc-400">
                                        <span x-show="conv.last_message && conv.last_message_sender_id === userId" class="text-zinc-500">You: </span><span x-text="conv.last_message || '{{ __('Say hi \uD83D\uDC4B') }}'"></span>
                                    </p>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>

                <template x-if="!loading && filteredConversations.length === 0">
                    <div class="flex h-full flex-col items-center justify-center gap-3 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full border border-zinc-800 bg-zinc-900">
                            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6" class="text-zinc-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-300" x-text="search ? '{{ __('No results found') }}' : '{{ __('No conversations yet') }}'"></p>
                            <p class="mt-1 text-xs text-zinc-600" x-show="search" x-text="`{{ __('Nothing matches') }} \u201c${search}\u201d`"></p>
                        </div>
                    </div>
                </template>
            </div>
            <button type="button" @click="jumpToTop()" x-show="!isAtTop && newActivityCount > 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5" style="position:absolute; top:16px; left:50%; transform:translateX(-50%); padding:8px 16px; border-radius:9999px; background:#6366f1; border:none; color:#fff; font-size:13px; box-shadow:0 4px 16px rgba(0,0,0,.4); cursor:pointer;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75 12 8.25l7.5 7.5"/>
                </svg>
                <span x-text="newActivityCount + ' new'"></span>
            </button>
        </div>
    </div>
</x-layouts::app>

{{ module_vite('build-chat', 'resources/assets/js/app.js') }}