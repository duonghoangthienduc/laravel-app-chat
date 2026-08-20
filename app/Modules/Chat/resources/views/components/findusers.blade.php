<div class="min-h-screen text-white" x-data="{ startingChat: false }" x-on:conversation-start-failed.window="startingChat = false">
    <!-- Full-page loading overlay -->
    <div x-show="startingChat" x-transition.opacity x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-3 rounded-2xl bg-zinc-900 border border-zinc-800 px-8 py-6 shadow-2xl">
            <svg class="h-8 w-8 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <p class="text-sm font-medium text-zinc-300">Starting conversation…</p>
        </div>
    </div>
    <!-- Header -->
    <div class="mx-auto">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-400">
                    Conversations </p>
                <h1 class="text-3xl font-semibold tracking-tight text-white">
                    Start a conversation </h1>
                <p class="mt-2 text-sm text-zinc-400">
                    Find a teammate and start chatting instantly. </p>
            </div>
            {{-- Wire this to a real online-count if you track presence; static text otherwise --}}
            @if(isset($onlineCount))
                <div class="rounded-full border border-zinc-800 bg-zinc-900 px-4 py-2 text-sm text-zinc-400">
                    <span class="mr-2 inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                    {{ $onlineCount }} people online
                </div>
            @endif
        </div>
        <!-- Search -->
        <div class="mb-8">
            <div class="group flex h-14 items-center gap-3 rounded-xl border border-zinc-800 bg-zinc-900/80 px-4 shadow-sm transition-all duration-200 focus-within:border-indigo-500/60 focus-within:bg-zinc-900 focus-within:ring-4 focus-within:ring-indigo-500/10">
                <svg class="h-5 w-5 shrink-0 text-zinc-500 transition-colors group-focus-within:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
                </svg>
                <label for="user-search" class="sr-only">Search people</label>
                <input id="user-search" wire:model.live.debounce.300ms="search" type="text" placeholder="Search people..." class="h-full flex-1 border-none bg-transparent p-0 text-sm text-white outline-none placeholder:text-zinc-600 focus:ring-0"/>
                <div wire:loading wire:target="search" class="shrink-0">
                    <svg class="h-4 w-4 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <!-- Section -->
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-sm font-semibold text-zinc-200">
                    People </h2>
                <span class="rounded-full bg-zinc-800 px-2.5 py-1 text-xs font-medium text-zinc-400">
                    {{ $users->total() }}
                </span>
            </div>
        </div>
        @if($users->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <svg class="mb-4 h-10 w-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
                </svg>
                <p class="font-medium text-zinc-400">No users found</p>
                @if($search)
                    <p class="mt-1 text-sm text-zinc-600">No results for "{{ $search }}"</p>
                @endif
            </div>
        @else
            <!-- Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 transition-opacity duration-150" wire:loading.class="opacity-40 pointer-events-none" wire:target="search">
                @foreach($users as $user)
                    @php
                        $colorPairs = [
                            ['#6366f1', '#7c3aed'], // indigo → violet
                            ['#f43f5e', '#f97316'], // rose → orange
                            ['#06b6d4', '#2563eb'], // cyan → blue
                            ['#10b981', '#0d9488'], // emerald → teal
                            ['#d946ef', '#db2777'], // fuchsia → pink
                            ['#f59e0b', '#ea580c'], // amber → orange
                        ];
                        $pairIndex = array_sum(array_map('mb_ord', mb_str_split($user->name))) % count($colorPairs);
                        [$fromHex, $toHex] = $colorPairs[$pairIndex];
                        $initial = mb_strtoupper(mb_substr($user->name, 0, 1));
                        $isOnline = $user->is_online ?? NULL;
                    @endphp
                    <div wire:key="user-{{ $user->id }}" wire:loading.class="opacity-50 pointer-events-none" wire:target="startConversation({{ $user->id }})" class="group relative overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500/40 hover:shadow-2xl hover:shadow-indigo-950/30">
                        <!-- Top accent -->
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/70 to-transparent opacity-0 transition group-hover:opacity-100"></div>
                        <!-- Card content -->
                        <div class="p-5">
                            <div class="mb-5 flex items-start justify-between">
                                <!-- Avatar -->
                                <div class="relative">
                                    <div style="width: 56px; height: 56px; border-radius: 9999px; background: linear-gradient(135deg, {{ $fromHex }}, {{ $toHex }}); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; color: #fff; box-shadow: 0 0 0 4px #18181b; flex-shrink: 0;" aria-hidden="true">
                                        {{ $initial }}
                                    </div>
                                    <span x-data :style="`position:absolute;bottom:0;right:0;width:14px;height:14px;border-radius:9999px;border:3px solid #18181b;background:${$store.onlinePresence?.isOnline({{ $user->id }}) ? '#34d399' : '#52525b'}`"></span>
                                </div>
                                <!-- Status -->
                                <span x-data x-text="$store.onlinePresence?.isOnline({{ $user->id }}) ? 'Online' : 'Offline'" :class="$store.onlinePresence?.isOnline({{ $user->id }}) ? 'rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-medium text-emerald-400' : 'rounded-full border border-zinc-700 bg-zinc-800 px-2.5 py-1 text-[11px] font-medium text-zinc-500'"></span>
                            </div>
                            <div class="wrap-user">
                                <h3 class="truncate text-base font-semibold text-white">
                                    {{ $user->name }}
                                </h3>
                                <p class="mt-1 truncate text-sm text-zinc-500">
                                    {{ $user->email }}
                                </p>
                            </div>
                            {{-- Optional role/department tags — wire to real fields if your User model has them --}}
                            @if(isset($user->role) || isset($user->department))
                                <div class="mt-5 flex items-center gap-2">
                                    @if(isset($user->role))
                                        <span class="rounded-md bg-indigo-500/10 px-2 py-1 text-[11px] font-medium text-indigo-400">
                                            {{ $user->role }}
                                        </span>
                                    @endif
                                    @if(isset($user->department))
                                        <span class="text-xs text-zinc-600">
                                            {{ $user->department }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <!-- Action -->
                        <!-- Action -->
                        <button type="button" @click="startingChat = true" wire:click="startConversation({{ $user->id }})" wire:loading.attr="disabled" wire:target="startConversation({{ $user->id }})" class="flex w-full items-center justify-center gap-2 border-t border-zinc-800 bg-zinc-950/40 px-5 py-3.5 text-sm font-medium text-zinc-300 transition hover:bg-indigo-500 hover:text-white disabled:cursor-wait disabled:opacity-70">
                            <span wire:loading.remove.flex wire:target="startConversation({{ $user->id }})" class="flex items-center gap-2">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z"/>
                                </svg>
                                Start Chat
                            </span>
                            <span wire:loading.flex wire:target="startConversation({{ $user->id }})" class="flex items-center gap-2">
                                <svg width="16" height="16" class="animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Connecting…
                            </span>
                        </button>
                    </div>
                @endforeach
            </div>
            @if($users->hasMorePages())
                <div class="mt-8 flex justify-center">
                    <button type="button" wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore" class="rounded-lg px-5 py-2.5 text-sm text-zinc-400 transition hover:bg-zinc-900 hover:text-white disabled:cursor-wait disabled:opacity-50">
                        <span wire:loading.remove wire:target="loadMore">Load more</span>
                        <span wire:loading wire:target="loadMore">Loading…</span>
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>