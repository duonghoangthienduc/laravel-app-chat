@once('chat-app-script')
    @push('module-head-scripts')
        {{ module_vite('build-chat', 'resources/assets/js/app.js') }}
    @endpush
@endonce
