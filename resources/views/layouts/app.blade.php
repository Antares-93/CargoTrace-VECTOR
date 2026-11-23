<!DOCTYPE html>
<html lang="en" id="html-root" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Control Room' }} • Vector Global Shipment Visibility</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN fallback + Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#F0FDF4',
                            100: '#DCFCE7',
                            200: '#BBF7D0',
                            500: '#10B981',
                            600: '#059669',
                            700: '#047857',
                            800: '#166534',
                            900: '#14532D',
                        },
                        navy: {
                            900: '#0F172A',
                            950: '#090D16',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
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
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Pulse Radar Animation */
        @keyframes radar-pulse {
            0% { transform: scale(0.9); opacity: 0.9; }
            70% { transform: scale(2.3); opacity: 0; }
            100% { transform: scale(0.9); opacity: 0; }
        }
        .radar-pulse-dot {
            position: relative;
        }
        .radar-pulse-dot::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 9999px;
            background: currentColor;
            opacity: 0.75;
            animation: radar-pulse 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>
    @stack('styles')

    <!-- Universal Scroll Preservation for Tab & Filter Switches -->
    <script>
        document.addEventListener('click', function(e) {
            const el = e.target.closest('a, button');
            if (!el) return;
            const href = el.getAttribute('href') || '';
            if (
                el.matches('.filter-tab, [data-preserve-scroll], .pagination a') ||
                href.includes('filter=') ||
                href.includes('status=') ||
                href.includes('mode=') ||
                href.includes('page=') ||
                el.type === 'submit'
            ) {
                sessionStorage.setItem('cargotrace_scroll_y', window.scrollY);
            }
        });

        (function() {
            const savedY = sessionStorage.getItem('cargotrace_scroll_y');
            if (savedY !== null) {
                sessionStorage.removeItem('cargotrace_scroll_y');
                window.scrollTo(0, parseInt(savedY, 10));
                window.addEventListener('DOMContentLoaded', function() {
                    window.scrollTo({
                        top: parseInt(savedY, 10),
                        behavior: 'instant'
                    });
                    setTimeout(function() {
                        window.scrollTo({
                            top: parseInt(savedY, 10),
                            behavior: 'instant'
                        });
                    }, 80);
                });
            }
        })();
    </script>

</head>
<body class="bg-[#F8FAFC] dark:bg-[#070A10] text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased selection:bg-emerald-500 selection:text-white transition-colors duration-200">

    <!-- Initialize Theme before DOM paints to prevent flash -->
    <script>
        const savedTheme = localStorage.getItem('cargotrace_theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        }
    </script>

    <!-- Top Navigation Header (Vector Forest Emerald Green Palette) -->
    <header class="w-full bg-white/95 dark:bg-[#0B0F17]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-40 transition-colors duration-200 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4 flex items-center justify-between gap-4">
            
            <!-- Left Logo & Brand Tag (Freestanding Vector Logo) -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0" title="Vector Home">
                <img src="/images/vector-logo-icon.png" alt="Vector Logo" class="w-9 h-9 object-contain group-hover:scale-105 transition transform">
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5">
                        VECTOR <span class="text-emerald-700 dark:text-emerald-400 text-xs px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 uppercase font-mono font-bold tracking-wider">CARGOTRACE</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium tracking-wide hidden sm:block">Global Multimodal Visibility</span>
                </div>
            </a>

            <!-- Center Horizontal Navigation Menu Links -->
            <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-xs font-bold text-slate-600 dark:text-slate-300">
                <a href="{{ route('dashboard') }}" 
                   class="transition py-1 {{ request()->routeIs('dashboard') ? 'text-emerald-600 dark:text-emerald-400 font-extrabold border-b-2 border-emerald-600 dark:border-emerald-400' : 'hover:text-emerald-600 dark:hover:text-white' }}"
                   data-i18n="nav_radar">
                    Overview &amp; Radar
                </a>

                <a href="{{ route('shipments.index') }}" 
                   class="transition py-1 {{ request()->routeIs('shipments.*') ? 'text-emerald-600 dark:text-emerald-400 font-extrabold border-b-2 border-emerald-600 dark:border-emerald-400' : 'hover:text-emerald-600 dark:hover:text-white' }}"
                   data-i18n="nav_consignments">
                    Consignments
                </a>

                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                <a href="{{ route('exceptions.index') }}" 
                   class="transition py-1 relative flex items-center gap-1.5 {{ request()->routeIs('exceptions.*') ? 'text-amber-500 font-extrabold border-b-2 border-amber-500' : 'hover:text-amber-500 dark:hover:text-white' }}"
                   data-i18n="nav_incidents">
                    <span>Incident Desk</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                </a>
                @endif

                <a href="{{ route('carriers.index') }}" 
                   class="transition py-1 {{ request()->routeIs('carriers.*') ? 'text-emerald-600 dark:text-emerald-400 font-extrabold border-b-2 border-emerald-600 dark:border-emerald-400' : 'hover:text-emerald-600 dark:hover:text-white' }}"
                   data-i18n="nav_carriers">
                    Carriers
                </a>

                <a href="{{ route('locations.index') }}" 
                   class="transition py-1 {{ request()->routeIs('locations.*') ? 'text-emerald-600 dark:text-emerald-400 font-extrabold border-b-2 border-emerald-600 dark:border-emerald-400' : 'hover:text-emerald-600 dark:hover:text-white' }}"
                   data-i18n="nav_nodes">
                    Hubs &amp; Nodes
                </a>

                @if (auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('audit.index') }}" 
                   class="transition py-1 {{ request()->routeIs('audit.*') ? 'text-emerald-600 dark:text-emerald-400 font-extrabold border-b-2 border-emerald-600 dark:border-emerald-400' : 'hover:text-emerald-600 dark:hover:text-white' }}"
                   data-i18n="nav_audit">
                    Audit Logs
                </a>
                @endif
            </nav>

            <!-- Right Actions & Utilities (Search, Alerts, Settings Modal, User Pill, Logout) -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                
                <!-- Quick Consignment Search Link -->
                <a href="{{ route('shipments.index') }}" 
                   class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700 hover:text-emerald-600 text-slate-600 dark:text-slate-300 flex items-center justify-center transition shadow-2xs" 
                   title="Search Consignments">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </a>

                @auth
                <!-- Notifications Bell Dropdown -->
                <div class="relative">
                    @php
                        $unreadNotifications = auth()->user()->notifications()->where('is_read', false)->get();
                    @endphp
                    <button onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')" 
                            class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700 hover:text-emerald-600 text-slate-600 dark:text-slate-300 flex items-center justify-center transition shadow-2xs relative"
                            title="Notifications">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if ($unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-500 text-white font-bold text-[9px] flex items-center justify-center animate-pulse">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-[#121A28] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl z-50 overflow-hidden">
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200" data-i18n="alerts_title">Notifications</span>
                            @if ($unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold hover:underline" data-i18n="mark_all_read">Mark all read</button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse(auth()->user()->notifications()->take(5)->get() as $n)
                            <form action="{{ route('notifications.read', $n) }}" method="POST" class="block hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                @csrf
                                <button type="submit" class="w-full text-left p-3 flex items-start gap-2.5">
                                    <div class="w-2 h-2 rounded-full mt-1.5 {{ $n->is_read ? 'bg-slate-300 dark:bg-slate-700' : 'bg-emerald-500' }}"></div>
                                    <div>
                                        <div class="text-xs font-semibold text-slate-800 dark:text-white">{{ $n->title }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">{{ $n->message }}</div>
                                        <div class="text-[9px] font-mono text-slate-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</div>
                                    </div>
                                </button>
                            </form>
                            @empty
                            <div class="p-6 text-center text-xs text-slate-400" data-i18n="no_notif">No new notifications.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Settings Gear Icon (Theme & Language Configuration Modal) -->
                <button type="button" onclick="openSettingsModal()" 
                        class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-emerald-600 flex items-center justify-center transition shadow-2xs" 
                        title="System Settings (Theme &amp; Language)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>

                <!-- User Profile Pill (Clickable -> View & Edit Profile) -->
                <a href="{{ route('profile.show') }}" 
                   class="flex items-center gap-2 pl-2 sm:pl-3 border-l border-slate-200/80 dark:border-slate-800 p-1 rounded-xl group hover:opacity-95 transition {{ request()->routeIs('profile.*') ? 'bg-emerald-50 dark:bg-emerald-950/40 ring-1 ring-emerald-500/30' : '' }}"
                   title="Lihat &amp; Edit Profil (View &amp; Edit Profile)">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 group-hover:bg-emerald-500 text-white font-extrabold text-xs flex items-center justify-center shadow-xs ring-2 ring-transparent group-hover:ring-emerald-400/50 transition">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 leading-tight transition">
                            {{ Str::limit(auth()->user()->name, 12) }}
                        </div>
                        <span class="text-[9px] font-mono px-2 py-0.5 rounded-full font-bold uppercase {{ auth()->user()->role === 'admin' ? 'bg-slate-900 text-white dark:bg-slate-800' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' }}">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </a>

                <!-- Clean Vector Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="inline ml-1">
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-full hover:bg-red-50 dark:hover:bg-red-950/40 text-slate-400 hover:text-red-500 flex items-center justify-center transition" title="Log Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-full shadow-sm hover:shadow transition" data-i18n="signin_btn">
                    Sign In
                </a>
                @endauth

            </div>

        </div>
    </header>

    <!-- Global Flash Notification Messages -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
        @if (session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-medium shadow-sm mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 text-base">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 dark:bg-red-950/80 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-medium shadow-sm mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-600 dark:text-red-400 text-base">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content Container (Full Width Max-7XL) -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-10">
        @yield('content')
    </main>

    <!-- Global Footer -->
    <footer class="w-full border-t border-slate-200/80 dark:border-slate-800 bg-white/60 dark:bg-[#070A10]/60 mt-16 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
            <div class="flex items-center gap-2.5">
                <img src="/images/vector-logo-icon.png" alt="Vector Logo" class="w-5 h-5 object-contain">
                <span class="font-bold text-slate-800 dark:text-slate-200">VECTOR CargoTrace</span>
                <span>&copy; {{ date('Y') }} Multimodal Visibility Network</span>
            </div>
            <div class="flex items-center gap-5 font-mono text-[11px]">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> AIS + ADS-B Live</span>
                <span>•</span>
                <span>OpenStreetMap GIS</span>
                <span>•</span>
                <span>Sanctum v3 Engine</span>
            </div>
        </div>
    </footer>

    <!-- System Settings Modal (Pure Vector SVGs) -->
    <div id="settings-modal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#121A28] border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" data-i18n="settings_title">Pengaturan Sistem</h3>
                </div>
                <button onclick="closeSettingsModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <!-- Appearance / Display Mode -->
            <div class="space-y-2.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block" data-i18n="theme_label">
                    Tampilan / Mode Layar
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="setTheme('light')" id="btn-theme-light" 
                            class="p-4 rounded-2xl border-2 flex flex-col items-center gap-2 transition font-bold text-xs bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white border-emerald-500 shadow-sm">
                        <!-- Pure Vector Sun Outline -->
                        <svg class="w-6 h-6 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32l1.41-1.41"/></svg>
                        <span data-i18n="theme_light">Mode Terang</span>
                        <span class="text-[10px] text-slate-400 font-normal" data-i18n="theme_light_desc">Terang &amp; Bersih</span>
                    </button>
                    <button type="button" onclick="setTheme('dark')" id="btn-theme-dark" 
                            class="p-4 rounded-2xl border-2 flex flex-col items-center gap-2 transition font-bold text-xs bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white border-transparent hover:border-slate-300 dark:hover:border-slate-700 shadow-sm">
                        <!-- Pure Vector Moon Outline -->
                        <svg class="w-6 h-6 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <span data-i18n="theme_dark">Mode Gelap</span>
                        <span class="text-[10px] text-slate-400 font-normal" data-i18n="theme_dark_desc">Radar Gelap</span>
                    </button>
                </div>
            </div>

            <!-- Language Selection -->
            <div class="space-y-2.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block" data-i18n="language_label">
                    Pilihan Bahasa
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="setLanguage('en')" id="btn-lang-en" 
                            class="p-3.5 rounded-2xl border-2 flex items-center justify-center gap-2.5 transition font-bold text-xs bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white border-transparent hover:border-slate-300 dark:hover:border-slate-700">
                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-extrabold bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200">US</span>
                        <span data-i18n="lang_en">English (EN)</span>
                    </button>
                    <button type="button" onclick="setLanguage('id')" id="btn-lang-id" 
                            class="p-3.5 rounded-2xl border-2 flex items-center justify-center gap-2.5 transition font-bold text-xs bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white border-emerald-500">
                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-extrabold bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200">ID</span>
                        <span data-i18n="lang_id">Indonesia (ID)</span>
                    </button>
                </div>
            </div>

            <div class="pt-3.5 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="button" onclick="closeSettingsModal()" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition" data-i18n="done_btn">
                    Selesai
                </button>
            </div>

        </div>
    </div>

    <!-- Multi-Language & Theme Controller Script -->
    <script>
        // Comprehensive Bilingual Dictionary (Translates Titles, Headers, Descriptions, Badges, Buttons, Tabs)
        const translations = {
            en: {
                nav_radar: "Overview & Radar",
                nav_consignments: "Consignments",
                nav_incidents: "Incident Desk",
                nav_carriers: "Carriers",
                nav_nodes: "Hubs & Nodes",
                nav_audit: "Audit Logs",
                signin_btn: "Sign In",
                settings_title: "System Settings",
                theme_label: "Appearance / Display Mode",
                theme_light: "Light Mode",
                theme_dark: "Dark Mode",
                theme_light_desc: "Crisp & Clean",
                theme_dark_desc: "Nautical Radar",
                language_label: "Language / Bahasa",
                lang_en: "English (EN)",
                lang_id: "Indonesia (ID)",
                done_btn: "Done",
                alerts_title: "Notifications",
                mark_all_read: "Mark all read",
                no_notif: "No new notifications.",
                
                // Dashboard Hero
                hero_pill: "LIVE RADAR & FLEET VISIBILITY",
                hero_headline: "Real-Time Multimodal Corridors & Fleet Visibility",
                hero_desc: "Monitor container ships and air freight across international maritime corridors and skyways with sub-hour AIS/ADS-B telemetry, automated ETA recalculation, and predictive delay intelligence.",
                hero_search_placeholder: "Enter Waybill, B/L, or AWB (e.g. AT-2026-B8910)",
                hero_track_btn: "Track Consignment",
                popular_tracks_label: "Popular tracks:",
                next_port: "NEXT PORT BERTHING",
                eta_countdown: "ETA COUNTDOWN",
                scheduled_label: "SCHEDULED:",
                telemetry_active_badge: "AIS + ADS-B ACTIVE",
                air_speed_tag: "AIR: 485 KTS",
                sea_delay_tag: "SEA DELAY: +28H",
                
                // Carrier Alliances
                carrier_alliance_title: "Global Integrated Alliances & Partner Fleets",
                
                // 4 KPI Cards
                total_consignments: "Total Consignments",
                active_global_corridors: "Active global corridors",
                in_transit_sub: "in transit",
                percent_active: "+100% active",
                air_express: "Air Express Lanes",
                flights_count: "3 Flights",
                high_priority_express: "High priority express",
                speed_kts: "Speed 485 Kts",
                ocean_lines: "Ocean Lines Fleet",
                vessels_count: "9 Vessels",
                partner_carriers_sub: "Partner carriers",
                carriers_list: "Maersk, EVG, MSC",
                ontime_reliability: "On-Time Reliability",
                multimodal_network_rate: "Multimodal network rate",
                benchmark_sub: "Benchmark: 90%",
                optimal_label: "Optimal",
                
                // Radar Map & Sub-Telemetry
                live_radar: "LIVE GIS AIS & ADS-B RADAR",
                focus_label: "FOCUS:",
                modes_legend: "Modes Legend",
                legend_ocean: "Ocean Vessel (Jakarta → Suez)",
                legend_remaining: "Remaining Ocean (Suez → Rotterdam)",
                legend_air: "Air Freight (Singapore → Frankfurt)",
                manifest_telemetry_title: "Tracked Cargo Manifest",
                manifest_telemetry_desc: "Ocean Container & Air Express",
                payload_title: "Combined Payload",
                payload_sub: "31,900.50 KG (Sea + Air)",
                air_eta_title: "Air Delivery ETA",
                air_eta_val: "Today • 21:30 UTC",
                voyage_milestones: "Voyage Milestones (Ocean Lead)",
                stage_active_sub: "Stage 3 of 4 Active",
                stage_1: "Stage 1",
                stage_2: "Stage 2",
                stage_3: "Stage 3",
                stage_4: "Stage 4",
                status_departed: "✓ Departed",
                status_cleared: "✓ Cleared",
                status_anchored: "Anchored (Queue)",
                status_scheduled: "Scheduled",
                
                // 3 Features section
                feature_subheading: "JOURNEY TO THE SKIES & SEAS MADE SIMPLE",
                feature_heading: "End-to-End Multimodal Intelligence",
                reefer_sensors: "Reefer Container Sensors",
                status_normal: "Normal",
                sensor_temp: "Container Temp",
                sensor_temp_sub: "±0.2°C Stable",
                sensor_humidity: "Humidity",
                sensor_humidity_sub: "Optimal",
                incident_notice: "Active Incident Notice",
                severity_high: "High",
                incident_title: "Suez Canal Convoy Delay",
                incident_desc: "Queue congestion at Suez entrance. Vessel delayed by +28 hours. Air flight QR-803F operating normally.",
                detected_ago: "Detected 9h ago",
                view_desk: "View Incident Desk",
                investigating_label: "Investigating",
                flight_telemetry: "Flight Telemetry (ADS-B)",
                flight_craft: "Flight / Craft",
                flight_speed_alt: "Air Speed & Alt",
                
                // Manifests Table
                active_manifests_title: "Active Consignment Manifests",
                active_manifests_sub: "Click any consignment row to zoom camera and track on the live map",
                all_consignments: "All Consignments",
                ocean_lines_tab: "Ocean Lines (Sea)",
                air_express_tab: "Air Express",
                exceptions_delay: "Exceptions (Delay)",
                new_manifest_btn: "New Manifest",
                tracking_id: "Tracking Identifier",
                mode: "Mode",
                consignee_carrier: "Consignee & Carrier",
                corridor_route: "Corridor Route",
                status: "Status",
                schedule_impact: "Schedule Impact",
                action: "Action",
                details_btn: "Details",
                on_schedule: "On Schedule",
                delay_suffix: "Delay",
                no_shipments_found: "No shipments found matching criteria.",

                // Consignments Index
                consignments_title: "Global Consignments Directory",
                consignments_sub: "Real-time tracking manifest for sea lines, air express, and multimodal logistics corridors.",
                register_new_shipment: "Register New Shipment",
                mode_all: "All Transport Modes",
                mode_ocean: "Ocean Lines",
                mode_air: "Air Express",
                mode_road: "Road Freight",
                status_all: "All Statuses",
                status_created: "Created",
                status_booked: "Booked",
                status_picked_up: "Picked Up",
                status_in_transit: "In Transit",
                status_in_flight: "In Flight",
                status_berthed: "Berthed",
                status_delivered: "Delivered",
                search_placeholder_consignments: "Search identifier, consignee...",

                // Incident Desk
                incident_desk_title: "Operational Incident Desk",
                incident_desk_sub: "Prioritize, investigate, and resolve maritime holds, severe weather diversions, and port convoy congestions.",
                ex_all: "All Incidents",
                ex_open: "Open",
                ex_investigating: "Investigating",
                ex_resolved: "Resolved",
                ex_shipment: "Shipment:",
                ex_carrier: "Carrier:",
                ex_route: "Route:",
                ex_resolution_notes: "Resolution Notes:",
                ex_btn_investigate: "Mark Investigating",
                ex_btn_resolve: "Resolve Case",

                // Carriers
                carrier_title: "Partner Carrier Registry",
                carrier_sub: "Authorized ocean shipping lines, air freight operators, and intermodal transport partners.",
                add_carrier: "Add New Carrier",

                // Hubs & Nodes
                hubs_title: "Global Hubs & Terminal Nodes",
                hubs_sub: "Strategic seaports, air cargo hubs, and inland logistics facilities.",
                add_node: "Add New Terminal",

                // Audit Logs
                audit_title: "Catatan Audit Aktivitas & Keamanan",
                audit_sub: "Rekaman kronologis yang tidak dapat diubah dari seluruh perubahan konsinyasi, pembaruan pelacakan, dan resolusi insiden.",
                audit_logged_entries: "Entri Tercatat",
                audit_col_time: "Waktu Kejadian",
                audit_col_user: "Pengguna / Pelaku",
                audit_col_action: "Aksi",
                audit_col_entity: "Entitas",
                audit_col_ip: "Alamat IP",
                audit_col_changes: "Perubahan Data / Snapshot",
                audit_no_events: "Belum ada catatan audit yang terekam.",
                audit_logged_entries: "Logged Entries",
                audit_col_time: "Timestamp",
                audit_col_user: "Actor / User",
                audit_col_action: "Action",
                audit_col_entity: "Entity",
                audit_col_ip: "IP Address",
                audit_col_changes: "Changes / Snapshot",
                audit_no_events: "No audit events recorded yet.",

                // Partners Wall
                partners_title: "OUR PARTNERS",
                view_all_partners: "View all",

                // Profile Page
                profile_active_badge: "Active Account",
                profile_member_since: "Member since:",
                back_to_dashboard: "Back to Dashboard",
                profile_metric_total_shipments: "Total Consignments",
                profile_metric_total_sub: "Cargo consignments registered under your name",
                profile_metric_active_transit: "In Transit",
                profile_metric_active_sub: "Active consignments tracked on radar",
                profile_metric_reported_incidents: "Incidents & Disruptions",
                profile_metric_incidents_sub: "Operational disruption reports for your cargo",
                profile_metric_telemetry_logged: "Telemetry Updates",
                profile_metric_telemetry_sub: "AIS & ADS-B GPS coordinates logged",
                profile_metric_exceptions_logged: "Incidents Handled",
                profile_metric_exceptions_sub: "Weather/port bottleneck cases recorded",
                profile_metric_active_corridors: "Active Corridors Fleet",
                profile_metric_corridors_sub: "Active consignments monitored on radar",
                profile_metric_system_shipments: "System Consignments",
                profile_metric_system_shipments_sub: "Total manifests registered in CargoTrace",
                profile_metric_audit_logs: "Security Audit Logs",
                profile_metric_audit_sub: "Historical change entries securely stored",
                profile_metric_active_consignments: "Active In-Flight/Sea",
                profile_metric_active_consignments_sub: "Vessels and planes in active movement",
                profile_form_title: "Personal Information",
                profile_form_sub: "Update your name, contact email address, and phone number.",
                profile_label_name: "Full Name",
                profile_label_email: "Email Address",
                profile_label_phone: "Phone / WhatsApp Number",
                profile_label_role: "Account Access Role",
                profile_role_readonly_tip: "Managed by System Administrator",
                profile_btn_save: "Save Changes",
                profile_password_title: "Security & Password",
                profile_password_sub: "Change your account password to keep your portal access secure.",
                profile_label_current_pwd: "Current Password",
                profile_label_new_pwd: "New Password",
                profile_pwd_hint: "Minimum 8 characters with a mix of letters and numbers.",
                profile_label_confirm_pwd: "Confirm New Password",
                profile_btn_change_pwd: "Update Password",
                profile_recent_shipments_title: "Your Recent Consignments",
                profile_recent_shipments_sub: "List of the 5 most recent shipments registered under your account.",
                profile_view_all_shipments: "View All →",
                profile_recent_telemetry_title: "Your Recent Telemetry & Coordinate Logs",
                profile_recent_telemetry_sub: "Latest GPS/AIS status coordinates submitted by you.",
                profile_recent_audit_title: "Account Security Activity Log",
                profile_recent_audit_sub: "Security audit trail of actions taken by this account."
            },
            id: {
                nav_radar: "Ringkasan & Radar",
                nav_consignments: "Konsinyasi",
                nav_incidents: "Meja Insiden",
                nav_carriers: "Operator",
                nav_nodes: "Pelabuhan & Hub",
                nav_audit: "Catatan Audit",
                signin_btn: "Masuk",
                settings_title: "Pengaturan Sistem",
                theme_label: "Tampilan / Mode Layar",
                theme_light: "Mode Terang",
                theme_dark: "Mode Gelap",
                theme_light_desc: "Terang & Bersih",
                theme_dark_desc: "Radar Gelap",
                language_label: "Pilihan Bahasa",
                lang_en: "Bahasa Inggris (EN)",
                lang_id: "Bahasa Indonesia (ID)",
                done_btn: "Selesai",
                alerts_title: "Pemberitahuan",
                mark_all_read: "Tandai dibaca",
                no_notif: "Tidak ada notifikasi baru.",
                
                // Dashboard Hero
                hero_pill: "RADAR LANGSUNG & VISIBILITAS ARMADA",
                hero_headline: "Pantau Koridor Multimodal & Armada Real-Time",
                hero_desc: "Pantau kapal kontainer dan kargo udara melintasi koridor laut internasional dan jalur penerbangan dengan telemetri AIS/ADS-B cepat, kalkulasi otomatis ETA, dan peringatan keterlambatan prediktif.",
                hero_search_placeholder: "Masukkan Nomor Resi, B/L, atau AWB (misal AT-2026-B8910)",
                hero_track_btn: "Lacak Konsinyasi",
                popular_tracks_label: "Resi populer:",
                next_port: "PELABUHAN BERLABUH BERIKUTNYA",
                eta_countdown: "HITUNG MUNDUR ETA",
                scheduled_label: "TERJADWAL:",
                telemetry_active_badge: "AIS + ADS-B AKTIF",
                air_speed_tag: "UDARA: 485 KTS",
                sea_delay_tag: "TUNDA LAUT: +28J",
                
                // Carrier Alliances
                carrier_alliance_title: "Aliansi Mitra & Armada Terintegrasi Global",
                
                // 4 KPI Cards
                total_consignments: "Total Konsinyasi",
                active_global_corridors: "Koridor global aktif",
                in_transit_sub: "dalam perjalanan",
                percent_active: "+100% aktif",
                air_express: "Jalur Kargo Udara",
                flights_count: "3 Penerbangan",
                high_priority_express: "Ekspres prioritas tinggi",
                speed_kts: "Kecepatan 485 Kts",
                ocean_lines: "Armada Jalur Laut",
                vessels_count: "9 Kapal",
                partner_carriers_sub: "Mitra operator",
                carriers_list: "Maersk, EVG, MSC",
                ontime_reliability: "Keandalan Tepat Waktu",
                multimodal_network_rate: "Tingkat jaringan multimodal",
                benchmark_sub: "Standar Acuan: 90%",
                optimal_label: "Optimal",
                
                // Radar Map & Sub-Telemetry
                live_radar: "RADAR LANGSUNG GIS AIS & ADS-B",
                focus_label: "FOKUS:",
                modes_legend: "Legenda Rute Navigasi",
                legend_ocean: "Kapal Laut (Jakarta → Suez)",
                legend_remaining: "Sisa Rute Laut (Suez → Rotterdam)",
                legend_air: "Kargo Udara (Singapura → Frankfurt)",
                manifest_telemetry_title: "Manifest Kargo Terpantau",
                manifest_telemetry_desc: "Kontainer Laut & Kargo Udara",
                payload_title: "Total Muatan Terpantau",
                payload_sub: "31.900,50 KG (Laut + Udara)",
                air_eta_title: "Estimasi Tiba Kargo Udara",
                air_eta_val: "Hari Ini • 21:30 UTC",
                voyage_milestones: "Tahapan Pelayaran (Jalur Laut Utama)",
                stage_active_sub: "Tahap 3 dari 4 Aktif",
                stage_1: "Tahap 1",
                stage_2: "Tahap 2",
                stage_3: "Tahap 3",
                stage_4: "Tahap 4",
                status_departed: "✓ Berangkat",
                status_cleared: "✓ Lolos Izin",
                status_anchored: "Lego Jangkar (Antre)",
                status_scheduled: "Terjadwal",
                
                // 3 Features section
                feature_subheading: "VISIBILITAS KORIDOR LAUT & UDARA TERPADU",
                feature_heading: "Intelijen Multimodal Terkoneksi Penuh",
                reefer_sensors: "Sensor Kontainer Reefer",
                status_normal: "Normal",
                sensor_temp: "Suhu Kontainer",
                sensor_temp_sub: "±0.2°C Stabil",
                sensor_humidity: "Kelembapan",
                sensor_humidity_sub: "Optimal",
                incident_notice: "Pemberitahuan Insiden Aktif",
                severity_high: "Tinggi",
                incident_title: "Keterlambatan Konvoi Terusan Suez",
                incident_desc: "Antrean kongesti di pintu masuk Terusan Suez. Kapal tertunda +28 jam. Penerbangan udara QR-803F beroperasi normal.",
                detected_ago: "Terdeteksi 9 jam lalu",
                view_desk: "Buka Meja Insiden",
                investigating_label: "Dalam Investigasi",
                flight_telemetry: "Telemetri Penerbangan (ADS-B)",
                flight_craft: "Penerbangan / Pesawat",
                flight_speed_alt: "Kecepatan Udara & Ketinggian",
                
                // Manifests Table
                active_manifests_title: "Daftar Manifest Konsinyasi Aktif",
                active_manifests_sub: "Klik baris konsinyasi untuk mengarahkan kamera dan melacak pada peta langsung",
                all_consignments: "Semua Konsinyasi",
                ocean_lines_tab: "Jalur Laut (Ocean)",
                air_express_tab: "Kargo Udara (Air)",
                exceptions_delay: "Kendala & Gangguan",
                new_manifest_btn: "Manifest Baru",
                tracking_id: "Nomor Pelacakan",
                mode: "Moda",
                consignee_carrier: "Penerima & Operator",
                corridor_route: "Rute Koridor",
                status: "Status",
                schedule_impact: "Dampak Jadwal",
                action: "Aksi",
                details_btn: "Rincian",
                on_schedule: "Tepat Waktu",
                delay_suffix: "Tertunda",
                no_shipments_found: "Tidak ada pengiriman yang cocok dengan kriteria.",

                // Consignments Index
                consignments_title: "Direktori Konsinyasi Global",
                consignments_sub: "Manifest pelacakan real-time untuk jalur laut, kargo udara, dan koridor logistik multimodal.",
                register_new_shipment: "Daftarkan Pengiriman Baru",
                mode_all: "Semua Moda Transportasi",
                mode_ocean: "Jalur Laut (Ocean)",
                mode_air: "Kargo Udara (Air)",
                mode_road: "Kargo Darat (Road)",
                status_all: "Semua Status",
                status_created: "Dibuat",
                status_booked: "Dipesan",
                status_picked_up: "Dijemput",
                status_in_transit: "Dalam Perjalanan",
                status_in_flight: "Sedang Terbang",
                status_berthed: "Bersandar",
                status_delivered: "Terkirim",
                search_placeholder_consignments: "Cari nomor resi, penerima...",

                // Incident Desk
                incident_desk_title: "Meja Insiden Operasional",
                incident_desk_sub: "Prioritaskan, investigasi, dan selesaikan kendala maritim, pengalihan rute cuaca, dan antrean Terusan Suez.",
                ex_all: "Semua Insiden",
                ex_open: "Terbuka",
                ex_investigating: "Dalam Investigasi",
                ex_resolved: "Terselesaikan",
                ex_shipment: "Pengiriman:",
                ex_carrier: "Operator:",
                ex_route: "Rute:",
                ex_resolution_notes: "Catatan Solusi:",
                ex_btn_investigate: "Tandai Investigasi",
                ex_btn_resolve: "Selesaikan Kasus",

                // Carriers
                carrier_title: "Direktori Operator & Mitra Armada",
                carrier_sub: "Daftar aliansi pelayaran maritim internasional dan maskapai kargo udara terintegrasi.",
                add_carrier: "Tambah Operator Baru",

                // Hubs & Nodes
                hubs_title: "Pelabuhan, Bandara & Terminal Transit",
                hubs_sub: "Titik simpul logistik global untuk alih muat kontainer dan kargo udara.",
                add_node: "Tambah Titik Baru",

                // Audit Logs
                audit_title: "Catatan Audit Sistem & Kepatuhan",
                audit_sub: "Rekaman jejak aktivitas dan modifikasi data yang tidak dapat diubah (immutable).",

                // Partners Wall
                partners_title: "MITRA & ALIANSI KAMI",
                view_all_partners: "Lihat semua",

                // Profile Page
                profile_active_badge: "Akun Aktif",
                profile_member_since: "Bergabung sejak:",
                back_to_dashboard: "Kembali ke Dashboard",
                profile_metric_total_shipments: "Total Konsinyasi",
                profile_metric_total_sub: "Konsinyasi kargo terdaftar atas nama Anda",
                profile_metric_active_transit: "Dalam Perjalanan",
                profile_metric_active_sub: "Konsinyasi aktif terpantau di radar",
                profile_metric_reported_incidents: "Insiden & Kendala",
                profile_metric_incidents_sub: "Laporan kendala operasional terkait kargo Anda",
                profile_metric_telemetry_logged: "Pembaruan Telemetri",
                profile_metric_telemetry_sub: "Log titik koordinat AIS & ADS-B tercatat",
                profile_metric_exceptions_logged: "Insiden Ditangani",
                profile_metric_exceptions_sub: "Kasus kendala cuaca/pelabuhan dicatat",
                profile_metric_active_corridors: "Armada Koridor Aktif",
                profile_metric_corridors_sub: "Konsinyasi aktif dalam pantauan radar",
                profile_metric_system_shipments: "Konsinyasi Sistem",
                profile_metric_system_shipments_sub: "Total manifest terdaftar di CargoTrace",
                profile_metric_audit_logs: "Log Audit Keamanan",
                profile_metric_audit_sub: "Entri riwayat perubahan tercatat aman",
                profile_metric_active_consignments: "Konsinyasi Berjalan",
                profile_metric_active_consignments_sub: "Kapal & pesawat aktif bertransportasi",
                profile_form_title: "Informasi Data Diri",
                profile_form_sub: "Perbarui nama, alamat email kontak, dan nomor telepon Anda.",
                profile_label_name: "Nama Lengkap",
                profile_label_email: "Alamat Email",
                profile_label_phone: "Nomor Telepon / WhatsApp",
                profile_label_role: "Hak Akses / Peran Akun",
                profile_role_readonly_tip: "Dikelola oleh Administrator",
                profile_btn_save: "Simpan Perubahan",
                profile_password_title: "Keamanan & Kata Sandi",
                profile_password_sub: "Ubah kata sandi akun untuk menjaga keamanan akses portal logistik.",
                profile_label_current_pwd: "Kata Sandi Saat Ini",
                profile_label_new_pwd: "Kata Sandi Baru",
                profile_pwd_hint: "Minimal 8 karakter kombinasi huruf dan angka.",
                profile_label_confirm_pwd: "Konfirmasi Kata Sandi Baru",
                profile_btn_change_pwd: "Perbarui Kata Sandi",
                profile_recent_shipments_title: "Konsinyasi Kargo Terbaru Anda",
                profile_recent_shipments_sub: "Daftar 5 pengiriman terbaru yang terdaftar atas nama akun Anda.",
                profile_view_all_shipments: "Lihat Semua →",
                profile_recent_telemetry_title: "Log Telemetri & Pembaruan Titik Terakhir Anda",
                profile_recent_telemetry_sub: "Pembaruan status GPS/AIS yang baru saja Anda inputkan ke sistem.",
                profile_recent_audit_title: "Catatan Aktivitas Keamanan Akun",
                profile_recent_audit_sub: "Audit trail keamanan atas aksi yang dilakukan akun ini."
            }
        };

        let currentLang = localStorage.getItem('cargotrace_lang') || 'id';

        function applyLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('cargotrace_lang', lang);
            
            // Update UI elements with data-i18n
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                        el.value = translations[lang][key];
                    } else if (el.children.length === 0) {
                        el.textContent = translations[lang][key];
                    } else {
                        // Update text child node
                        const textNode = Array.from(el.childNodes).find(n => n.nodeType === Node.TEXT_NODE && n.textContent.trim().length > 0);
                        if (textNode) {
                            textNode.textContent = translations[lang][key];
                        } else {
                            el.innerText = translations[lang][key];
                        }
                    }
                }
            });

            // Update placeholder inputs with data-i18n-placeholder
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                const key = el.getAttribute('data-i18n-placeholder');
                if (translations[lang] && translations[lang][key]) {
                    el.placeholder = translations[lang][key];
                }
            });

            // Update modal buttons active state
            const btnEn = document.getElementById('btn-lang-en');
            const btnId = document.getElementById('btn-lang-id');
            if (btnEn && btnId) {
                if (lang === 'en') {
                    btnEn.classList.add('border-emerald-500');
                    btnEn.classList.remove('border-transparent');
                    btnId.classList.remove('border-emerald-500');
                    btnId.classList.add('border-transparent');
                } else {
                    btnId.classList.add('border-emerald-500');
                    btnId.classList.remove('border-transparent');
                    btnEn.classList.remove('border-emerald-500');
                    btnEn.classList.add('border-transparent');
                }
            }
        }

        function setLanguage(lang) {
            applyLanguage(lang);
        }

        function setTheme(theme) {
            localStorage.setItem('cargotrace_theme', theme);
            const root = document.documentElement;
            const btnLight = document.getElementById('btn-theme-light');
            const btnDark = document.getElementById('btn-theme-dark');

            if (theme === 'dark') {
                root.classList.add('dark');
                root.classList.remove('light');
                if (btnDark && btnLight) {
                    btnDark.classList.add('border-emerald-500');
                    btnDark.classList.remove('border-transparent');
                    btnLight.classList.remove('border-emerald-500');
                    btnLight.classList.add('border-transparent');
                }
            } else {
                root.classList.remove('dark');
                root.classList.add('light');
                if (btnLight && btnDark) {
                    btnLight.classList.add('border-emerald-500');
                    btnLight.classList.remove('border-transparent');
                    btnDark.classList.remove('border-emerald-500');
                    btnDark.classList.add('border-transparent');
                }
            }

            // Invalidate map size and redraw tiles if map exists
            if (window.map) {
                setTimeout(() => window.map.invalidateSize(), 150);
            }
        }

        function openSettingsModal() {
            document.getElementById('settings-modal').classList.remove('hidden');
        }

        function closeSettingsModal() {
            document.getElementById('settings-modal').classList.add('hidden');
        }

        // Apply on initial load
        document.addEventListener('DOMContentLoaded', () => {
            const currentTheme = localStorage.getItem('cargotrace_theme') || 'light';
            setTheme(currentTheme);
            applyLanguage(currentLang);
        });
    </script>

    @stack('scripts')
</body>
</html>
