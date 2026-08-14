<x-layouts::app :title="__('Chat')">
    <div x-data="chatInbox({{ auth()->id() }}, @js($activeConversationId))" class="flex h-[calc(100vh-4rem)] flex-col overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center gap-3 border-b border-zinc-800 px-4 py-3">
            <a href="{{ route('chat.inbox') }}" wire:navigate class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </a>

            <template x-if="activeConversation">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(activeConversation?.other_name)" x-text="getInitials(activeConversation?.other_name)"></div>
                    <div class="min-w-0">
                        <p class="truncate text-[15px] font-semibold text-white" x-text="activeConversation?.other_name"></p>
                        <p class="text-xs text-zinc-500">{{ __('Direct message') }}</p>
                    </div>
                </div>
            </template>

            <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div x-ref="scrollBox" style="flex:1; overflow-y:auto; padding:24px 16px;">
            <div style="width:100%; margin-left:auto; margin-right:auto;">
                <template x-if="loadingMessages">
                    <div class="flex h-full items-center justify-center">
                        <svg width="32" height="32" class="animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </div>
                </template>

                <template x-if="!loadingMessages && messages.length === 0 && !otherTyping">
                    <div class="flex flex-col items-center gap-3 pt-24 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full text-xl font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(activeConversation?.other_name)" x-text="getInitials(activeConversation?.other_name)"></div>
                        <div>
                            <p class="text-sm font-medium text-white" x-text="activeConversation?.other_name"></p>
                            <p class="mt-1 text-xs text-zinc-500">{{ __('This is the beginning of your conversation') }}</p>
                        </div>
                    </div>
                </template>

                <template x-if="!loadingMessages && (messages.length > 0 || otherTyping)">
                    <div class="space-y-6">
                        <template x-for="group in groupedMessages" :key="group.items[0].id">
                            <div class="flex w-full gap-3" :class="group.sender_id === userId ? 'flex-row-reverse' : 'justify-start'">
                                <div class="w-9 shrink-0" x-show="group.sender_id !== userId">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(group.sender_name)" x-text="getInitials(group.sender_name)"></div>
                                </div>
                                <div style="display:flex; flex-direction:column; max-width:75%;" :class="group.sender_id === userId ? 'items-end' : 'items-start'">
                                    <span class="mb-1 px-1 text-xs font-semibold text-zinc-300" x-show="group.sender_id !== userId" x-text="group.sender_name"></span>

                                    <div class="flex flex-col gap-[3px]" :class="group.sender_id === userId ? 'items-end' : 'items-start'">
                                        <template x-for="(msg, idx) in group.items" :key="msg.id">
                                            <div>
                                                <div style="word-break:break-word; overflow-wrap:break-word; max-width:100%; width:fit-content;" :style="group.sender_id !== userId ? 'background:#141416; border:1px solid rgba(255,255,255,.08); word-break:break-word; overflow-wrap:break-word; max-width:100%; width:fit-content;' : 'word-break:break-word; overflow-wrap:break-word; max-width:100%; width:fit-content;'" class="rounded-2xl px-4 py-2.5 text-[15px] leading-relaxed" :class="[
                                                            group.sender_id === userId ? 'bg-indigo-500 text-white' : 'text-zinc-100',
                                                            group.items.length > 1
                                                                ? (idx === 0
                                                                    ? (group.sender_id === userId ? 'rounded-br-md' : 'rounded-bl-md')
                                                                    : idx === group.items.length - 1
                                                                        ? (group.sender_id === userId ? 'rounded-tr-md' : 'rounded-tl-md')
                                                                        : (group.sender_id === userId ? 'rounded-r-md' : 'rounded-l-md'))
                                                                : '',
                                                        ]" x-text="msg.content"></div>

                                                <div class="mt-0.5 flex items-center gap-1 px-1" x-show="group.sender_id === userId && (msg._pending || msg._failed)">
                                                    <svg x-show="msg._pending" width="10" height="10" class="animate-spin text-zinc-500" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                    </svg>
                                                    <span x-show="msg._pending" class="text-[11px] text-zinc-500">{{ __('Sending…') }}</span>
                                                    <span x-show="msg._failed" class="text-[11px] text-red-400">{{ __('Failed to send') }}</span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <span class="mt-1 px-1 text-[11px] text-zinc-600" x-text="group.items[group.items.length - 1].created_at"></span>
                                </div>
                            </div>
                        </template>

                        {{-- Typing indicator --}}
                        <div class="flex w-full items-end gap-3" x-show="otherTyping" x-transition.opacity>
                            <div class="w-9 shrink-0">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(activeConversation?.other_name)" x-text="getInitials(activeConversation?.other_name)"></div>
                            </div>
                            <div class="flex items-center gap-1 rounded-2xl px-4 py-3" style="background:#141416; border:1px solid rgba(255,255,255,.08);">
                                <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-500" style="animation-delay:0ms"></span>
                                <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-500" style="animation-delay:150ms"></span>
                                <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-500" style="animation-delay:300ms"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Input --}}
        <div style="border-top:1px solid #27272a; padding:12px 16px;">
            <div style="width:100%; margin-left:auto; margin-right:auto;">
                <form @submit.prevent="send()" class="flex items-end gap-2 rounded-3xl border border-zinc-800 bg-zinc-900/80 px-3 py-2">
                    <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </button>
                    <textarea x-model="draft" @input="notifyTyping()" @keydown.enter.prevent="send()" rows="1" placeholder="{{ __('Type a message here') }}" class="max-h-32 flex-1 resize-none bg-transparent py-1.5 text-[15px] text-white outline-none placeholder:text-zinc-600"></textarea>
                    <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z"/>
                        </svg>
                    </button>

                    <button type="submit" :disabled="!draft.trim()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-white transition hover:bg-indigo-400 disabled:cursor-not-allowed disabled:opacity-40">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.77 59.77 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.874L5.999 12Zm0 0h7.5"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts::app>

{{ module_vite('build-chat', 'resources/assets/js/app.js') }}