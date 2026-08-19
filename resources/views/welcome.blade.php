<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Simple App Chat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.head')
    <style>
		.glass-card {
			background: rgba(255, 255, 255, 0.03);
			backdrop-filter: blur(10px);
			border: 1px solid rgba(255, 255, 255, 0.08);
		}
		.message-bubble {
			position: relative;
			max-width: 85%;
		}
		.gradient-text {
			background: linear-gradient(135deg, #FF4433 0%, #F8B803 50%, #FF4433 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
		}
		.glow-red {
			box-shadow: 0 0 40px rgba(245, 48, 3, 0.15);
		}
    </style>
</head>
<body>
<div class="bg-[#0a0a0a] text-white min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">
    <!-- Background decorative elements -->
    <div class="absolute top-20 left-10 w-72 h-72 bg-red-500/5 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-orange-500/5 rounded-full blur-3xl animate-float-delayed"></div>

    <!-- Floating chat icons -->
    <div class="absolute top-32 right-20 opacity-20 animate-float">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-red-400">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
    </div>
    <div class="absolute bottom-40 left-16 opacity-15 animate-float-delayed">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-orange-400">
            <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
    </div>

    <!-- Header / Navigation -->
    <header class="w-full max-w-5xl flex items-center justify-between mb-8 animate-fade-in">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center shadow-lg shadow-red-500/20">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <span class="font-semibold text-lg tracking-tight">Simple App Chat</span>
        </div>
        @if (Route::has('login'))
            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 text-sm text-gray-400 hover:text-white transition-colors duration-200">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 text-sm text-gray-400 hover:text-white transition-colors duration-200">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-2 text-sm bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-lg transition-all duration-200">Register</a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-5xl grid lg:grid-cols-2 gap-8 items-center animate-slide-up">

        <!-- Left: Chat Preview -->
        <div class="space-y-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                    Live Chat Platform
                </div>
                <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                    Connect with<br>
                    <span class="gradient-text">anyone, anywhere</span>
                </h1>
                <p class="text-gray-400 text-lg leading-relaxed max-w-md">
                    A simple, fast, and secure messaging platform. Start conversations, share moments, and stay connected with the people who matter. </p>
            </div>

            <!-- Mock Chat Interface -->
            <div class="glass-card rounded-2xl p-4 space-y-3 max-w-md glow-red">
                <div class="flex items-center gap-2 pb-3 border-b border-white/5">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <span class="text-xs text-gray-500 ml-2">Chat Preview</span>
                </div>

                <!-- Messages -->
                <div class="space-y-3 py-2">
                    <div class="flex gap-2 items-end">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-[10px] font-bold shrink-0">JD</div>
                        <div class="message-bubble received bg-white/5 rounded-2xl rounded-bl-sm px-3 py-2 text-sm text-gray-300">
                            Hey! Have you tried the new chat app? 🚀
                        </div>
                    </div>

                    <div class="flex gap-2 items-end justify-end">
                        <div class="message-bubble sent bg-red-500/20 rounded-2xl rounded-br-sm px-3 py-2 text-sm text-white">
                            Yes! The interface is so clean and fast ⚡
                        </div>
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-400 to-orange-500 flex items-center justify-center text-[10px] font-bold shrink-0">ME</div>
                    </div>

                    <div class="flex gap-2 items-end">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-[10px] font-bold shrink-0">JD</div>
                        <div class="message-bubble received bg-white/5 rounded-2xl rounded-bl-sm px-3 py-2 text-sm text-gray-300">
                            Let's start a group chat with the team!
                        </div>
                    </div>

                    <!-- Typing indicator -->
                    <div class="flex gap-2 items-end">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-[10px] font-bold shrink-0">JD</div>
                        <div class="bg-white/5 rounded-2xl rounded-bl-sm px-4 py-3 flex gap-1">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-typing"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-typing" style="animation-delay: 0.2s"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-typing" style="animation-delay: 0.4s"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-wrap gap-3 pt-2">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 rounded-xl font-medium text-sm transition-all duration-200 shadow-lg shadow-red-500/25 hover:shadow-red-500/40 hover:-translate-y-0.5">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    Start Chatting
                </a>
            </div>

            <!-- Stats -->
            <div class="flex gap-6 pt-4 border-t border-white/5">
                <div>
                    <div class="text-2xl font-bold text-white">10k+</div>
                    <div class="text-xs text-gray-500">Active Users</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white">99.9%</div>
                    <div class="text-xs text-gray-500">Uptime</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white">&lt;50ms</div>
                    <div class="text-xs text-gray-500">Latency</div>
                </div>
            </div>
        </div>

        <!-- Right: Laravel Branding -->
        <div class="relative flex items-center justify-center">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-orange-500/10 rounded-3xl blur-2xl"></div>
            <div class="relative bg-[#1D0002] rounded-2xl p-8 lg:p-12 border border-red-500/20 overflow-hidden">
                <!-- Laravel Logo -->
                <svg class="w-full max-w-[320px] text-[#F53003] mb-4" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor"/>
                    <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor"/>
                    <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337ZM241.31 75.7472C240.312 78.4531 238.916 80.8078 237.122 82.8112C235.326 84.8161 233.156 86.4183 230.614 87.6206C228.07 88.823 225.251 89.4238 222.16 89.4238C219.068 89.4238 216.275 88.823 213.782 87.6206C211.289 86.4183 209.145 84.8161 207.35 82.8112C205.554 80.8078 204.182 78.4531 203.236 75.7472C202.286 73.0421 201.814 70.1868 201.814 67.1821C201.814 64.1758 202.286 61.3205 203.236 58.6154C204.182 55.9102 205.554 53.5571 207.35 51.5522C209.145 49.5495 211.288 47.9451 213.782 46.7427C216.275 45.5404 219.068 44.9396 222.16 44.9396C225.251 44.9396 228.07 45.5404 230.614 46.7427C233.156 47.9451 235.326 49.5487 237.122 51.5522C238.917 53.5571 240.312 55.9102 241.31 58.6154C242.306 61.3205 242.806 64.1758 242.806 67.1821C242.805 70.1868 242.305 73.0421 241.31 75.7472Z" fill="currentColor"/>
                    <path d="M438 -3H421.694V102.197H438V-3Z" fill="currentColor"/>
                    <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="currentColor"/>
                    <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="currentColor"/>
                    <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656ZM357.02 61.0967C357.145 59.7487 359.023 43.3761 376.442 43.3761C393.861 43.3761 395.978 59.7464 396.099 61.0967H357.02Z" fill="currentColor"/>
                </svg>

                <div class="text-center space-y-2">
                    <div class="text-6xl font-bold text-[#F53003]">13</div>
                    <div class="text-sm text-red-400/60 font-medium tracking-widest uppercase">Powered by Laravel</div>
                </div>

                <!-- Decorative circles -->
                <div class="absolute -top-10 -right-10 w-32 h-32 border border-red-500/10 rounded-full"></div>
                <div class="absolute -bottom-10 -left-10 w-24 h-24 border border-red-500/10 rounded-full"></div>
            </div>
        </div>
    </main>

    <!-- Footer features -->
    <div class="w-full max-w-5xl mt-12 grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in" style="animation-delay: 0.3s">
        <div class="glass-card rounded-xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-green-500/10 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div>
                <div class="text-sm font-medium">End-to-End</div>
                <div class="text-xs text-gray-500">Encryption</div>
            </div>
        </div>
        <div class="glass-card rounded-xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-500/10 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                </svg>
            </div>
            <div>
                <div class="text-sm font-medium">Real-Time</div>
                <div class="text-xs text-gray-500">Messaging</div>
            </div>
        </div>
        <div class="glass-card rounded-xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-purple-500/10 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="text-sm font-medium">Group</div>
                <div class="text-xs text-gray-500">Chat Rooms</div>
            </div>
        </div>
        <div class="glass-card rounded-xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-orange-500/10 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div>
                <div class="text-sm font-medium">Secure &</div>
                <div class="text-xs text-gray-500">Private</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>