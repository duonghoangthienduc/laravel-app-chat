<x-layouts::app :title="__('Chat')">
    <div x-data="chatInbox({{ auth()->id() }}, @js($activeConversationId), @js($initialMedia))" class="flex h-[calc(100vh-4rem)] flex-col overflow-hidden transition-[filter] duration-500" :style="connectionState !== 'connected' ? 'filter:saturate(0.7) brightness(0.95);' : ''">
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
                        <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-[#0b0d10] transition-colors duration-500" :class="{
                                    'bg-emerald-400 animate-pulse': connectionState === 'connected',
                                    'bg-amber-400': connectionState === 'connecting',
                                    'bg-red-500': connectionState === 'unavailable' || connectionState === 'disconnected',
                                }"></span>
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

                    <template x-if="!loadingMessages && messages.length === 0 && !otherTyping">
                        <div class="flex flex-col items-center gap-4 pt-24 text-center">
                            <template x-if="!activeConversation">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="h-20 w-20 animate-pulse rounded-full bg-zinc-800"></div>
                                    <div class="space-y-2">
                                        <div class="mx-auto h-4 w-28 animate-pulse rounded bg-zinc-800"></div>
                                        <div class="mx-auto h-3 w-40 animate-pulse rounded bg-zinc-800/70"></div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="activeConversation">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="relative">
                                        <div class="flex h-20 w-20 items-center justify-center rounded-full text-2xl font-semibold text-white ring-4 ring-white/10" :style="avatarStyle(activeConversation?.other_name)" x-text="getInitials(activeConversation?.other_name)"></div>
                                        <span class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-full border-2 border-[#0b0d10]" style="background:#141416;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" class="text-indigo-400">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-white" x-text="activeConversation?.other_name"></p>
                                        <p class="mt-1 text-sm text-zinc-500">{{ __('Say hi and start the conversation') }}</p>
                                    </div>
                                </div>
                            </template>
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
                                                            <div style="display: contents">
                                                                <div x-show="msg.media && msg.media.length > 0" class="mb-1 grid gap-1" :style="`grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); width: fit-content; ${group.sender_id === userId ? 'margin-left:auto;' : 'margin-right:auto;'}`">
                                                                    <template x-for="m in (msg.media || [])" :key="m.id">
                                                                        <img :src="m.url" style="width:100%; border-radius:14px; object-fit:cover; max-height:220px; cursor:pointer; display:block;" @click="window.open(m.url, '_blank')"/>
                                                                    </template>
                                                                </div>

                                                                <div x-show="msg.content && msg.content.trim() !== ''" style="word-break:break-word; overflow-wrap:break-word; max-width:100%; width:fit-content; border-radius:20px; backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);" :style="(group.sender_id === userId
                                                                            ? 'padding:10px 16px 10px 16px; background:rgba(99,102,241,.85);'
                                                                            : 'padding:10px 16px 10px 16px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08);')
                                                                            + 'word-break:break-word; overflow-wrap:break-word; max-width:100%; width:fit-content; border-radius:20px; backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);'" class="text-[15px] leading-relaxed" :class="[
                                                                            group.sender_id === userId ? 'text-white' : 'text-zinc-100',
                                                                            msg._pending ? 'opacity-60' : '',
                                                                            msg._failed ? 'ring-2 ring-red-500/60' : '',
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
                                    </div>
                                </div>
                            </template>

                            <div class="flex w-full items-end gap-3" x-show="otherTyping" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="w-9 shrink-0">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(activeConversation?.other_name)" x-text="getInitials(activeConversation?.other_name)"></div>
                                </div>
                                <div style="display:flex; align-items:center; gap:5px; border-radius:20px; padding:12px 16px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08); backdrop-filter:blur(10px);">
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
                <div x-show="pendingMedia.length > 0" class="mb-2 flex flex-wrap gap-2" style="position:relative;">
                    <template x-for="media in pendingMedia" :key="media.id">
                        <div style="position:relative; width:64px; height:64px;">
                            <img :src="media.url" style="width:100%; height:100%; object-fit:cover; border-radius:10px;" :style="isSendingMedia ? 'opacity:0.4;' : ''"/>
                            <button type="button" @click="removePendingMedia(media.id)" x-show="!isSendingMedia" style="position:absolute; top:-6px; right:-6px; width:18px; height:18px; border-radius:9999px; background:#18181b; border:1px solid rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; color:#fff; cursor:pointer;">
                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <div x-show="isSendingMedia" style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.3); border-radius:10px;">
                        <svg width="20" height="20" class="animate-spin" style="color:#818cf8;" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity:.2" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path style="opacity:.9" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </div>
                </div>

                <form @submit.prevent="send()" class="flex items-end gap-2 rounded-3xl border border-zinc-800 bg-zinc-900/80 px-3 py-2">
                    <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept="image/*" multiple class="hidden"/>
                    <button type="button" x-show="mediaEnabled" @click="openFilePicker()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </button>
                    <textarea x-model="draft" @input="notifyTyping()" @keydown.enter.prevent="send()" @paste="handlePaste($event)" rows="1" placeholder="{{ __('Type a message here') }}" class="max-h-32 flex-1 resize-none bg-transparent py-1.5 text-[15px] text-white outline-none placeholder:text-zinc-600"></textarea>
                    <button type="submit" :disabled="(!draft.trim() && pendingMedia.length === 0) || isSendingMedia" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-white transition hover:bg-indigo-400 disabled:cursor-not-allowed disabled:opacity-40">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.77 59.77 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.874L5.999 12Zm0 0h7.5"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts::app>

@assets{{ module_vite('build-chat', 'resources/assets/js/app.js') }}@endassets