<div class="h-full flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
        <div>
            <h1 class="text-xl font-semibold text-white">{{ __('New Chat') }}</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ __('Find users and start a conversation') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-neutral-500 px-2 py-1 rounded-md bg-white/5">
                {{ $users->total() }} {{ __('users') }}
            </span>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="px-6 py-4 border-b border-white/5 space-y-3">
        <div class="relative">
            <flux:icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-neutral-500"/>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Search by name or email...') }}" class="w-full pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/20 transition-all"/>
        </div>
    </div>

    <!-- Users List -->
    <div class="flex-1 overflow-y-auto px-2 py-2">
        @if($users->isEmpty())
            <div class="flex flex-col items-center justify-center h-64 text-center">
                <div class="w-14 h-14 rounded-full bg-white/5 flex items-center justify-center mb-3">
                    <flux:icon name="magnifying-glass" class="size-6 text-neutral-500"/>
                </div>
                <p class="text-sm text-neutral-400 font-medium">{{ __('No users found') }}</p>
                <p class="text-xs text-neutral-600 mt-1">{{ __('Try adjusting your search or filters') }}</p>
            </div>
        @else
            <div class="space-y-1">
                @foreach($users as $user)
                    <div class="group flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-colors cursor-pointer" wire:click="startConversation({{ $user->id }})" wire:navigate>
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover"/>
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <!-- Online indicator -->
                            @if($user->last_seen_at && $user->last_seen_at->diffInMinutes(now()) < 5)
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-[#1a1a1a] rounded-full"></span>
                            @else
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-neutral-600 border-2 border-[#1a1a1a] rounded-full"></span>
                            @endif
                        </div>


                        <!-- Action -->
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                            <button class="p-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 transition-colors">
                                <flux:icon name="chat-bubble-left" class="size-4"/>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($users->hasMorePages())
                <div class="mt-4 flex justify-center">
                    <button wire:click="$dispatch('load-more')" class="text-xs text-neutral-500 hover:text-white transition-colors px-4 py-2 rounded-lg hover:bg-white/5">
                        {{ __('Load more...') }}
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>
