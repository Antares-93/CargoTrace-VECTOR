<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CargoTrace' }} • Vector Global Shipment Visibility & Tracking</title>
    
    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN fallback + Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            850: '#1e5a3f',
                            900: '#14532d',
                            950: '#052e16',
                        },
                        radar: {
                            bg: '#0B0F17',
                            surface: '#111827',
                            card: '#161F30',
                            cardBorder: '#23324D',
                            border: '#1F2937',
                            accent: '#10B981',
                            ocean: '#38BDF8',
                            air: '#22C55E',
                            warning: '#F59E0B',
                            danger: '#EF4444',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- Leaflet.js CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B0F17;
            color: #F3F4F6;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0B0F17;
        }
        ::-webkit-scrollbar-thumb {
            background: #23324D;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #10B981;
        }
        /* Leaflet Dark Filter */
        .radar-map .leaflet-tile-pane {
            filter: brightness(0.7) invert(1) contrast(3) hue-rotate(200deg) saturate(0.35);
        }
        /* Pulse Radar Animation */
        @keyframes radar-pulse {
            0% {
                transform: scale(0.9);
                opacity: 0.9;
            }
            70% {
                transform: scale(2.2);
                opacity: 0;
            }
            100% {
                transform: scale(0.9);
                opacity: 0;
            }
        }
        .radar-dot {
            position: relative;
        }
        .radar-dot::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 9999px;
            background: currentColor;
            opacity: 0.75;
            animation: radar-pulse 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-[#0B0F17] text-slate-100 flex flex-col selection:bg-emerald-500 selection:text-white">

    <!-- Top Radar Ticker Header (matching Image 6) -->
    <div class="bg-[#080B10] border-b border-[#1E293B] text-[11px] font-mono tracking-wider px-4 lg:px-8 py-1.5 flex flex-wrap items-center justify-between text-slate-400">
        <div class="flex items-center gap-4 lg:gap-6 overflow-x-auto py-0.5">
            <span class="flex items-center gap-1.5 text-slate-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>TERMINAL HUBS</span>
            </span>
            <span class="text-slate-600">•</span>
            <span class="hover:text-slate-200 transition">FLEET REGISTRY</span>
            <span class="text-slate-600">•</span>
            <span class="hover:text-slate-200 transition">CUSTOMS COMPLIANCE</span>
            <span class="text-slate-600">•</span>
            <span class="flex items-center gap-1.5 text-cyan-400 font-semibold">
                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                LIVE AIS &amp; ADS-B RADAR
            </span>
        </div>
        <div class="flex items-center gap-4 mt-1 sm:mt-0 font-medium">
            <span class="text-slate-400">LOCAL <strong id="clock-local" class="text-slate-200">07:06</strong></span>
            <span class="text-slate-600">|</span>
            <span class="text-slate-400">PORT TIME <span class="bg-[#162032] px-1.5 py-0.5 rounded text-cyan-300">UTC+1</span></span>
            <span class="text-slate-600">|</span>
            <span class="bg-amber-950/80 text-amber-300 border border-amber-700/60 px-2 py-0.5 rounded-full text-[10px] font-bold flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                {{ $activeDelaysCount ?? 3 }} ACTIVE DELAYS
            </span>
        </div>
    </div>

    <!-- Main Navigation Bar (Image 6 layout) -->
    <header class="bg-[#101726] border-b border-[#1F2E47] sticky top-0 z-40 shadow-xl">
        <div class="max-w-[1720px] mx-auto px-4 lg:px-8 flex items-center justify-between h-16">
            
            <!-- Brand Logo with Vector Emblem -->
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <img src="/images/vector-logo-icon.png" alt="Vector CargoTrace Logo" class="w-9 h-9 object-contain drop-shadow-[0_0_12px_rgba(34,197,94,0.35)] group-hover:scale-105 transition transform">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-lg tracking-wider text-white">VECTOR</span>
                            <span class="text-xs px-1.5 py-0.5 bg-emerald-950/90 text-emerald-400 border border-emerald-600/40 rounded font-mono font-bold">CargoTrace</span>
                        </div>
                        <div class="text-[10px] tracking-widest text-slate-400 font-mono uppercase -mt-0.5">Trace the Line</div>
                    </div>
                </a>

                <!-- Navigation Tabs -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-1.5 rounded-md text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('dashboard') ? 'bg-[#1E2E47] text-emerald-400 border-b-2 border-emerald-400 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#162235]' }}">
                        Overview
                    </a>
                    <a href="{{ route('shipments.index') }}" class="px-3.5 py-1.5 rounded-md text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('shipments.*') ? 'bg-[#1E2E47] text-emerald-400 border-b-2 border-emerald-400 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#162235]' }}">
                        Consignments
                    </a>
                    @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                    <a href="{{ route('exceptions.index') }}" class="px-3.5 py-1.5 rounded-md text-xs font-semibold uppercase tracking-wider transition flex items-center gap-1.5 {{ request()->routeIs('exceptions.*') ? 'bg-[#1E2E47] text-amber-400 border-b-2 border-amber-400' : 'text-slate-300 hover:text-white hover:bg-[#162235]' }}">
                        Incident Desk
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    </a>
                    @endif
                    <a href="{{ route('carriers.index') }}" class="px-3.5 py-1.5 rounded-md text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('carriers.*') ? 'bg-[#1E2E47] text-emerald-400 border-b-2 border-emerald-400' : 'text-slate-300 hover:text-white hover:bg-[#162235]' }}">
                        Carriers
                    </a>
                    <a href="{{ route('locations.index') }}" class="px-3.5 py-1.5 rounded-md text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('locations.*') ? 'bg-[#1E2E47] text-emerald-400 border-b-2 border-emerald-400' : 'text-slate-300 hover:text-white hover:bg-[#162235]' }}">
                        Hubs &amp; Nodes
                    </a>
                    @if (auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('audit.index') }}" class="px-3.5 py-1.5 rounded-md text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('audit.*') ? 'bg-[#1E2E47] text-cyan-400 border-b-2 border-cyan-400' : 'text-slate-300 hover:text-white hover:bg-[#162235]' }}">
                        Audit Logs
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Right Controls: Notifications & User Profile Pill -->
            <div class="flex items-center gap-3">
                
                @auth
                <!-- In-App Notification Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    @php
                        $unreadNotifications = auth()->user()->notifications()->where('is_read', false)->get();
                    @endphp
                    <button onclick="document.getElementById('notif-menu').classList.toggle('hidden')" class="relative p-2 rounded-lg bg-[#162235] hover:bg-[#1E2E47] text-slate-300 hover:text-white border border-[#23324D] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if ($unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center animate-pulse">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Notifications Panel Dropdown -->
                    <div id="notif-menu" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-[#131B2E] border border-[#23324D] rounded-xl shadow-2xl z-50 overflow-hidden">
                        <div class="px-4 py-3 bg-[#162035] border-b border-[#23324D] flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-200">Alerts &amp; Notifications</span>
                            @if ($unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[11px] text-emerald-400 hover:underline">Mark all read</button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-[#1E293B]">
                            @forelse(auth()->user()->notifications()->take(6)->get() as $notif)
                                <form action="{{ route('notifications.read', $notif) }}" method="POST" class="block hover:bg-[#1A253D] transition">
                                    @csrf
                                    <button type="submit" class="w-full text-left p-3.5 flex items-start gap-3">
                                        <div class="mt-0.5 w-2 h-2 rounded-full {{ $notif->is_read ? 'bg-slate-600' : 'bg-emerald-400' }}"></div>
                                        <div>
                                            <div class="text-xs font-semibold {{ $notif->is_read ? 'text-slate-300' : 'text-white' }}">{{ $notif->title }}</div>
                                            <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-2">{{ $notif->message }}</div>
                                            <div class="text-[9px] font-mono text-slate-500 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                                        </div>
                                    </button>
                                </form>
                            @empty
                                <div class="p-6 text-center text-xs text-slate-400">No recent notifications.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Profile Pill (matching Image 6) -->
                <div class="flex items-center gap-2 bg-[#162235] border border-[#23324D] rounded-lg px-3 py-1.5">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-emerald-600 to-cyan-500 text-white font-bold text-xs flex items-center justify-center">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-semibold text-white leading-tight flex items-center gap-1.5">
                            {{ Str::limit(auth()->user()->name, 18) }}
                            <span class="text-[9px] px-1.5 py-0.2 rounded font-mono uppercase font-bold {{ auth()->user()->role === 'admin' ? 'bg-purple-950 text-purple-300 border border-purple-700/50' : (auth()->user()->role === 'operator' ? 'bg-cyan-950 text-cyan-300 border border-cyan-700/50' : 'bg-emerald-950 text-emerald-300 border border-emerald-700/50') }}">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="ml-1">
                        @csrf
                        <button type="submit" title="Log Out" class="text-xs text-slate-400 hover:text-red-400 px-1.5 py-1 rounded hover:bg-[#1E2E47] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
                @else
                <a href="{{ route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow transition">
                    Sign In
                </a>
                @endauth

            </div>
        </div>
    </header>

    <!-- Global Flash Notification Messages -->
    <div class="max-w-[1720px] mx-auto px-4 lg:px-8 mt-4 w-full">
        @if (session('success'))
            <div class="bg-emerald-950/80 border border-emerald-600 text-emerald-200 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-lg mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-950/80 border border-red-600 text-red-200 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-lg mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-950/80 border border-red-600 text-red-200 px-4 py-3 rounded-xl text-sm shadow-lg mb-4">
                <div class="font-bold flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-2">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 w-full max-w-[1720px] mx-auto px-4 lg:px-8 py-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#080B10] border-t border-[#1E293B] py-6 px-4 lg:px-8 text-xs text-slate-400 mt-auto">
        <div class="max-w-[1720px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="/images/vector-logo-icon.png" class="w-5 h-5 object-contain opacity-70">
                <span class="font-mono text-slate-300 font-bold">CargoTrace • Vector Enterprise Visibility</span>
                <span class="text-slate-600">•</span>
                <span>UAS Web Programming 2 Portfolio</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400 font-mono text-[11px]">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> MySQL 8.x Active</span>
                <span>Laravel 12.x REST API</span>
                <span>OpenStreetMap &amp; Leaflet</span>
                <span>Open-Meteo Weather API</span>
            </div>
        </div>
    </footer>

    <!-- Clock Script -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const el = document.getElementById('clock-local');
            if (el) el.innerText = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    @stack('scripts')
</body>
</html>
