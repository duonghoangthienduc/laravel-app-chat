{{-- Header --}}
<div class="flex items-center gap-3 border-b border-zinc-800 px-4 py-3">
    <a href="{{ route('chat.inbox') }}" wire:navigate class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
    </a>
    <div class="flex min-w-0 flex-1 items-center gap-3">
        <div class="relative shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold text-white ring-2 ring-white/10" :style="avatarStyle(activeConversation?.other_name)" x-text="getInitials(activeConversation?.other_name)"></div>
            <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-1 transition-colors duration-500" :class="$store.onlinePresence?.isOnline(activeConversation?.other_user_id) ? 'bg-emerald-400': 'bg-zinc-500'"></span>
        </div>
        <div class="min-w-0">
            <p class="truncate text-[15px] font-semibold text-white" x-text="activeConversation?.other_name"></p>
            <p class="text-xs" :class="$store.onlinePresence?.isOnline(activeConversation?.other_user_id) ? 'text-emerald-400' : 'text-amber-500/80'" x-text="$store.onlinePresence?.isOnline(activeConversation?.other_user_id) ?  '{{ __('Online') }}' : '{{ __('Offline') }}'"></p>
        </div>
    </div>
</div>