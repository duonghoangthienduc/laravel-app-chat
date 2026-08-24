<x-layouts::app.sidebar :title="$title ?? NULL">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>