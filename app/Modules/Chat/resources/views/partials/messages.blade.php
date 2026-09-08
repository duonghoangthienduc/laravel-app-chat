{{-- Messages --}}
<div class="messages">
    <div x-ref="scrollBox" @scroll="onScroll()" class="scrollBox">
        <div x-ref="contentBox" class="contentBox">
            <template x-if="loadingMessages">
                <div class="spinnerContainer">
                    <svg width="36" height="36" class="animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:.2" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path style="opacity:.9" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <p class="text">{{ __('Loading messages…') }}</p>
                </div>
            </template>

            <template x-if="!loadingMessages && messages.length === 0 && !otherTyping">
                <div class="flex flex-col items-center gap-4 pt-24 text-center">
                    <template x-if="activeConversation">
                        <div class="flex flex-col items-center gap-1">
                            <div class="m-auto">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-[#0b0d10]" style="background:#141416;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" class="text-indigo-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-zinc-500">{{ __('Say hi and start the conversation') }}</p>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="!loadingMessages && (messages.length > 0 || otherTyping)">
                <div class="space-y-2">
                    <div x-show="loadingOlder" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex justify-center" style="padding:0.25rem 0 1.25rem;">
                        <div class="loadingOlder">
                            <svg width="16" height="16" class="animate-spin" style="color:#818cf8;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity:.2" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity:.9" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </div>
                    </div>
                    <template x-for="(day, dayIdx) in groupedByDay" :key="day.label + '-' + dayIdx">
                        <div class="chat-zone">
                            <div class="groupDateTime">
                                <span class="dateTimeLabel" x-text="day.label"></span>
                            </div>
                            <div class="space-y-6">
                                <template x-for="group in day.groups" :key="group.items[0].id">
                                    <div class="flex w-full gap-3" :class="group.sender_id === userId ? 'flex-row-reverse' : 'justify-start'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                        <div class="w-9 shrink-0" x-show="group.sender_id !== userId">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(group.sender_name)" x-text="getInitials(group.sender_name)"></div>
                                        </div>
                                        <div class="disFlexColumn" :class="group.sender_id === userId ? 'items-end' : 'items-start'">
                                            <span class="mb-1 px-1 text-xs font-semibold text-zinc-300" x-show="group.sender_id !== userId" x-text="group.sender_name"></span>
                                            <div class="disFlexColumn gap-1.5" :class="group.sender_id === userId ? 'items-end' : 'items-start'">
                                                <template x-for="(msg, idx) in group.items" :key="msg.id">
                                                    <div class="contents">
                                                        @include('chat::partials.message-media')
                                                        <div x-show="msg.content && msg.content.trim() !== ''" :class="messageContentClasses(group, msg)" :style="messageContentStyles(group)" x-text="msg.content"></div>
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
                        <div class="sendingStatus">
                            <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400" style="animation-delay:0ms"></span>
                            <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400" style="animation-delay:150ms"></span>
                            <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <button type="button" @click="jumpToNewest()" x-show="!isNearBottom" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 jumpToNewest">
        <span x-show="newMessageCount > 0" x-text="newMessageCount + ' new message'"></span>
        <span x-show="newMessageCount === 0">{{ __('Jump to latest') }}</span>
    </button>
</div>