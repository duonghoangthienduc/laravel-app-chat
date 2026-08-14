@foreach (Navigation::groups() as $group)
    <flux:sidebar.nav>
        <flux:sidebar.group :heading="$group['heading']" class="grid">
            @foreach ($group['items'] as $item)
                <flux:sidebar.item :icon="$item['icon']" :href="$item['href']" :current="$item['active']" wire:navigate>
                    {{ $item['label'] }}
                </flux:sidebar.item>

                @if ($item['children']->isNotEmpty())
                    @include($item['children_view'], ['children' => $item['children']])
                @endif
            @endforeach
        </flux:sidebar.group>
    </flux:sidebar.nav>
@endforeach