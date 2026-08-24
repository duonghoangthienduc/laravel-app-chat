<x-chat::layouts.chat :title="__('Chat')">
    <div x-data="chatInbox(
        {{ auth()->id() }},
        @js($activeConversationId),
        @js($activeConversation),
        @js($initialMessages ?? []),
        @js($initialNextCursor ?? NULL),
        @js($activeConversationId !== NULL)
    )" class="flex h-[calc(100vh-4rem)] flex-col overflow-hidden transition-[filter] duration-500" :style="connectionState !== 'connected' ? 'filter:saturate(0.7) brightness(0.95);' : ''">
        {{-- Header --}}
        <div class="flex items-center gap-3 border-b border-zinc-800 px-4 py-3">
            <a href="{{ route('chat.inbox') }}" wire:navigate class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <template x-if="activeConversation">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <div class="relative shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(activeConversation?.other_name)" x-text="getInitials(activeConversation?.other_name)"></div>
                        <span x-show="!activeConversation?.is_group && activeConversation?.other_user_id" class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-[#0b0d10]" :class="$store.onlinePresence?.isOnline(activeConversation?.other_user_id) ? 'bg-emerald-400' : 'bg-zinc-700'"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-[15px] font-semibold text-white" x-text="activeConversation?.other_name"></p>
                        <p class="text-xs" :class="connectionState === 'connected' ? 'text-zinc-500' : 'text-amber-500/80'" x-text="connectionState === 'connected' ? '{{ __('Direct message') }}' : '{{ __('Reconnecting…') }}'"></p>
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
        <div style="position:relative; flex:1; min-height:0; display:flex; flex-direction:column;">
            <div x-ref="scrollBox" @scroll="onScroll()" style="flex:1; overflow-y:auto; padding:24px 16px; scroll-behavior:smooth;">
                <div style="width:100%; margin-left:auto; margin-right:auto;">
                    <template x-if="loadingMessages">
                        <div style="display:flex; height:100%; min-height:400px; flex-direction:column; align-items:center; justify-content:center; gap:14px;">
                            <svg width="36" height="36" class="animate-spin" style="color:#818cf8;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity:.2" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity:.9" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <p style="font-size:13px; color:#71717a;">{{ __('Loading messages…') }}</p>
                        </div>
                    </template>

                    {{-- Case: conversation mới, chưa resolve được trong danh sách (đang "setting up") --}}
                    <template x-if="!loadingMessages && messages.length === 0 && !otherTyping && !activeConversation">
                        <div class="flex h-full flex-col items-center justify-center gap-5 px-6 pb-20 text-center">
                            <svg width="36" height="36" class="animate-spin" style="color:#818cf8;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity:.2" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity:.9" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <div class="text-xs text-zinc-500">
                                {{ __('Setting up conversation') }}
                                <p class="leading-relaxed text-zinc-600">
                                    {{ __('Preparing your secure chat environment. This will only take a moment.') }}
                                </p>
                            </div>
                        </div>
                    </template>

                    {{-- Case: conversation đã xác nhận, thật sự chưa có tin nhắn --}}
                    <template x-if="!loadingMessages && messages.length === 0 && !otherTyping && activeConversation">
                        <div class="flex h-full flex-col items-center justify-center gap-3 px-6 pb-20 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-800/60">
                                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6" class="text-zinc-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.97-4.03 9-9 9a8.96 8.96 0 0 1-4.32-1.1L3 21l1.25-3.75A8.96 8.96 0 0 1 3 12c0-4.97 4.03-9 9-9s9 4.03 9 9Z"/>
                                </svg>
                            </div>
                            <div class="text-xs text-zinc-500">
                                {{ __('No messages yet') }}
                                <p class="leading-relaxed text-zinc-600">{{ __('Say hi to start the conversation.') }}</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="!loadingMessages && (messages.length > 0 || otherTyping)">
                        <div class="space-y-2">
                            <div x-show="loadingOlder" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex justify-center" style="padding:4px 0 20px;">
                                <div style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:9999px; background:#18181b; border:1px solid rgba(255,255,255,.1);">
                                    <svg width="16" height="16" class="animate-spin" style="color:#818cf8;" fill="none" viewBox="0 0 24 24">
                                        <circle style="opacity:.2" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                        <path style="opacity:.9" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </div>
                            </div>
                            <template x-for="(day, dayIdx) in groupedByDay" :key="day.label + '-' + dayIdx">
                                <div>
                                    {{-- Day divider --}}
                                    <div style="display:flex; justify-content:center; margin:8px 0 16px;">
                                        <span style="background:rgba(255,255,255,.06); color:#a1a1aa; font-size:12px; padding:4px 12px; border-radius:9999px;" x-text="day.label"></span>
                                    </div>

                                    <div class="space-y-6">
                                        <template x-for="group in day.groups" :key="group.items[0].id">
                                            <div class="flex w-full gap-3" :class="group.sender_id === userId ? 'flex-row-reverse' : 'justify-start'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                                <div class="w-9 shrink-0" x-show="group.sender_id !== userId">
                                                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(group.sender_name)" x-text="getInitials(group.sender_name)"></div>
                                                </div>
                                                <div style="display:flex; flex-direction:column; max-width:75%;" :class="group.sender_id === userId ? 'items-end' : 'items-start'">
                                                    <span class="mb-1 px-1 text-xs font-semibold text-zinc-300" x-show="group.sender_id !== userId" x-text="group.sender_name"></span>

                                                    <div style="display:flex; flex-direction:column; gap:6px;" :class="group.sender_id === userId ? 'items-end' : 'items-start'">
                                                        <template x-for="(msg, idx) in group.items" :key="msg.id">
                                                            <div x-transition:enter="transition ease-out duration-350" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                                                                <div :style="(group.sender_id === userId ? 'padding:10px 16px 10px 16px; background:rgba(99,102,241,.85);' : 'padding:10px 16px 10px 16px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08);') + 'word-break:break-word; white-space:pre-wrap; overflow-wrap:break-word; max-width:100%; width:fit-content; border-radius:20px; backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);'" class="text-[15px] leading-relaxed" :class="[group.sender_id === userId ? 'text-white' : 'text-zinc-100', msg._pending ? 'opacity-60' : '', msg._failed ? 'ring-2 ring-red-500/60' : '']" x-text="msg.content"></div>
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
                                    </div>
                                </div>
                            </template>
                            <div class="flex w-full items-end gap-3" x-show="otherTyping" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="w-9 shrink-0">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(activeConversation?.other_name)" x-text="getInitials(activeConversation?.other_name)"></div>
                                </div>
                                <div style="display:flex; align-items:center; gap:5px; border-radius:20px; padding:12px 20px 12px 16px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08); backdrop-filter:blur(10px);">
                                    <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400" style="animation-delay:0ms"></span>
                                    <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400" style="animation-delay:150ms"></span>
                                    <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400" style="animation-delay:300ms"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <button type="button" @click="jumpToNewest()" x-show="!isNearBottom" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5" style="position:absolute; bottom:16px; left:50%; transform:translateX(-50%); padding:8px 16px; border-radius:9999px; background:#18181b; border:1px solid rgba(255,255,255,.1); color:#fff; font-size:13px; box-shadow:0 4px 16px rgba(0,0,0,.4); cursor:pointer;">
                <span x-show="newMessageCount > 0" x-text="newMessageCount + ' new'"></span>
                <span x-show="newMessageCount === 0">{{ __('Jump to latest') }}</span>
            </button>
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
                    <textarea x-model="draft" x-ref="draftInput" @input="notifyTyping(); autoGrow()" @keydown.enter="handleEnter($event)" rows="1" placeholder="{{ __('Type a message here') }}" style="max-height: 200px; overflow-y: auto;" class="flex-1 resize-none bg-transparent py-1.5 text-[15px] text-white outline-none placeholder:text-zinc-600"></textarea>
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
</x-chat::layouts.chat>

@assets{{ module_vite('build-chat', 'resources/assets/js/app.js') }}@endassets