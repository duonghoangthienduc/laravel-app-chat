@php
    $seedData = $children->map(fn ($child) => [
        'id'      => $child['id'],
        'label'   => $child['label'],
        'href'    => $child['href'],
        'active'  => $child['active'],
        'initial' => $child['initial'],
    ])->values();
@endphp

<div style="margin-left: .75rem" x-data="chatSidebar({{ auth()->id() }}, @js($seedData), 10)">
    <button type="button" @click="toggleOpen()" style="display: flex; width: 100%; align-items: center; justify-content: space-between; padding: 6px 8px; font-size: 12px; font-weight: 500; color: #71717a; background: transparent; border: none; outline: none; cursor: pointer;">
        <span>{{ __('Direct messages') }}</span>
        <svg width="14" height="14" style="flex-shrink: 0; transition: transform .2s;" :style="open ? 'transform: rotate(90deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
        </svg>
    </button>

    <div x-show="open" x-transition.opacity.duration.150ms class="ml-4 space-y-0.5 border-l border-zinc-800 pl-3">
        <template x-for="conv in visibleConversations" :key="conv.id">
            <a :href="conv.href" wire:navigate class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm transition" :class="conv.active ? 'bg-indigo-500/15 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                <span class="relative flex shrink-0 items-center justify-center rounded-full text-[10px] font-semibold text-white ring-1 ring-white/10" :style="avatarStyle(conv.label)" x-text="conv.initial"></span>
                <span class="truncate" x-text="conv.label"></span>
            </a>
        </template>

        <button type="button" x-show="hasMore" @click="showAll = !showAll" style="
                    display: flex;
                    width: 100%;
                    align-items: center;
                    gap: 8px;
                    padding: 6px 8px;
                    border-radius: 8px;
                    font-size: 14px;
                    color: #71717a;
                    background: transparent;
                    border: none;
                    outline: none;
                    cursor: pointer;">
            <svg width="14" height="14" style="flex-shrink: 0; transition: transform .2s;" :style="showAll ? 'transform: rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
            <span x-text="showAll ? '{{ __('Show less') }}' : '{{ __('Show all') }}'"></span>
        </button>
    </div>
</div>