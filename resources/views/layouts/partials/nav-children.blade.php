<div class="ml-4 space-y-0.5 border-l border-zinc-800 pl-3">
    @foreach ($children as $child)
        <a href="{{ $child['href'] }}" wire:navigate @class(['flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm transition', 'bg-indigo-500/15 text-white' => $child['active'], 'text-zinc-400 hover:bg-zinc-900 hover:text-white' => ! $child['active']])>
            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-zinc-700 text-[10px] font-semibold text-white">
                {{ $child['initial'] }}
            </span>
            <span class="truncate">
                {{ $child['label'] }}
            </span>
        </a>
    @endforeach
</div>