<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-[#0a0a0a] text-white antialiased">
<div class="relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden p-6 md:p-10">

    {{-- Decorative glow, same as homepage --}}
    <div class="absolute top-20 left-10 h-72 w-72 rounded-full bg-red-500/5 blur-3xl animate-float"></div>
    <div class="absolute bottom-20 right-10 h-96 w-96 rounded-full bg-orange-500/5 blur-3xl animate-float-delayed"></div>

    <div class="relative flex w-full max-w-sm flex-col gap-2">
        <a href="{{ route('home') }}" wire:navigate class="mb-4 inline-flex items-center gap-2 self-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-gray-300 transition-all duration-200 hover:border-white/20 hover:bg-white/10 hover:text-white">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"/>
                <path d="M12 19l-7-7 7-7"/>
            </svg>
            {{ __('Back to home page') }}
        </a>
        {{-- Glass card wraps the form --}}
        <div class="glass-card rounded-2xl p-8 flex flex-col gap-6 shadow-xl shadow-black/40">
            {{ $slot }}
        </div>
    </div>
</div>

@persist('toast')
<flux:toast.group>
    <flux:toast/>
</flux:toast.group>
@endpersist

@fluxScripts

<style>
	.glass-card {
		background: rgba(255, 255, 255, 0.03);
		backdrop-filter: blur(10px);
		border: 1px solid rgba(255, 255, 255, 0.08);
	}
</style>
</body>
</html>