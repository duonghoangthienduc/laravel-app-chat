<div class="h-full flex flex-col">
    <style>
		.start-chat-btn {
			transition: background-color .2s ease, box-shadow .2s ease, transform .1s ease;
			cursor: pointer;
		}
		.start-chat-btn:hover {
			background-color: #819aac;
			color: #fff;
		}
    </style>
    <!-- Header -->
    <div class="px-8 pt-10 pb-6">
        <h1 class="text-[26px] font-bold text-white tracking-tight">New Chat</h1>
        <p class="text-neutral-500 mt-1.5 text-[15px]">Find someone to start a conversation with</p>
    </div>

    <!-- Search -->
    <div class="px-8 pb-6">
        <label for="user-search" class="sr-only">Search users</label>
        <div class="relative flex items-center gap-3 bg-[#252525] rounded-xl px-4 py-3 border border-white/[0.06] focus-within:border-red-500/60 focus-within:ring-2 focus-within:ring-red-500/20 focus-within:bg-[#2a2a2a] transition-all duration-200">
            <svg class="w-5 h-5 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="user-search" wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..." class="flex-1 bg-transparent text-[15px] text-white placeholder-neutral-600 outline-none border-none p-0 h-6"/>
            <div wire:loading.delay wire:target="search" class="shrink-0">
                <svg class="w-4 h-4 text-red-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Section Label -->
    <div class="px-8 py-2">
        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">People &mdash; {{ $users->total() }}</span>
    </div>

    <!-- Grid -->
    <div class="flex-1 overflow-y-auto px-8 pb-8">
        @if($users->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <svg class="w-10 h-10 text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-neutral-400 font-medium">No users found</p>
                @if($search)
                    <p class="text-neutral-600 text-sm mt-1">No results for "{{ $search }}"</p>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($users as $user)
                    @php
                        $colors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899'];
                        $colorIndex = array_sum(array_map('mb_ord', mb_str_split($user->name))) % count($colors);
                        $bgColor = $colors[$colorIndex];
                        $initial = mb_strtoupper(mb_substr($user->name, 0, 1));
                    @endphp
                    <div wire:key="user-{{ $user->id }}" class="group relative flex flex-col bg-[#1c1c1c] rounded-2xl border border-white/[0.06] overflow-hidden
                                   transition-all duration-200 hover:border-red-500/25 hover:shadow-[0_8px_30px_rgba(0,0,0,0.35)] hover:-translate-y-1">
                        <!-- Optional status badge, wire it to a real field when you have one e.g. $user->is_online -->
                        @if(isset($user->is_online) && $user->is_online)
                            <span class="absolute top-4 left-4 z-10 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">
                                online
                            </span>
                        @endif

                        <!-- Avatar area -->
                        <div class="flex flex-col items-center py-2 px-6">
                            <div class="w-20 h-20 aspect-square shrink-0 rounded-full flex items-center justify-center text-white text-2xl font-bold ring-4 ring-white/[0.04] overflow-hidden" style="background-color: {{ $bgColor }}; padding: 1.2rem" aria-hidden="true">
                                <span class="leading-none">{{ $initial }}</span>
                            </div>
                            <div class="mt-4 text-[16px] font-semibold text-white text-center truncate w-full">
                                {{ $user->name }}
                            </div>
                            <div class="text-sm text-neutral-500 text-center truncate w-full">
                                {{ $user->email }}
                            </div>
                        </div>

                        <button type="button" wire:click="startConversation({{ $user->id }})" class="start-chat-btn w-full flex items-center justify-center gap-2 py-3 text-[13px] font-semibold tracking-wide whitespace-nowrap text-neutral-300 bg-white/[0.04] border-t border-white/[0.08]">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Start Chat</span>
                        </button>

                    </div>
                @endforeach
            </div>

            @if($users->hasMorePages())
                <div class="mt-8 flex justify-center">
                    <button type="button" wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore" class="text-sm text-neutral-400 hover:text-white px-5 py-2.5 rounded-lg hover:bg-white/[0.04] transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-wait">
                        <span wire:loading.remove wire:target="loadMore">Load more</span>
                        <span wire:loading wire:target="loadMore">Loading…</span>
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>
