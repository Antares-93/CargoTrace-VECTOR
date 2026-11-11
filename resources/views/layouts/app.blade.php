<!DOCTYPE html>
<html lang="en" id="html-root" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CargoTrace' }} • Vector Global Shipment Visibility</title>
    
    <!-- Google Fonts: Inter & JetBrains Mono -->
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
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#1e5a3f',
                            900: '#14532d',
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
</head>
<body class="min-h-screen bg-[#EAEFF5] dark:bg-[#070A10] text-slate-800 dark:text-slate-100 p-2 sm:p-4 lg:p-6 transition-colors duration-200">

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

    <!-- Master Tablet Shell Container (exact styling from user's design reference) -->
    <div class="max-w-[1780px] mx-auto min-h-[92vh] bg-white dark:bg-[#0B0F17] rounded-[28px] sm:rounded-[36px] shadow-2xl border border-slate-200/80 dark:border-slate-800/80 flex flex-col md:flex-row overflow-hidden transition-colors duration-200">
        
        <!-- Left Vertical Dark Icon Sidebar (matching Image 2) -->
        <aside class="w-full md:w-20 lg:w-24 bg-[#111625] dark:bg-[#080C14] text-slate-400 p-3 sm:p-4 flex md:flex-col items-center justify-between border-b md:border-b-0 md:border-r border-slate-800 shrink-0 z-30">
            
            <!-- Top Logo Badge (Creamy Rounded Box with Vector Icon) -->
            <div class="flex md:flex-col items-center gap-4">
                <a href="{{ route('dashboard') }}" class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl bg-[#FBF6E9] dark:bg-emerald-950/70 border border-amber-200/50 dark:border-emerald-600/40 p-2 flex items-center justify-center shadow-md hover:scale-105 transition transform group" title="Vector CargoTrace">
                    <img src="/images/vector-logo-icon.png" alt="Vector Logo" class="w-full h-full object-contain">
                </a>
            </div>

            <!-- Middle Navigation Icons -->
            <nav class="flex md:flex-col items-center gap-2 sm:gap-3 my-2 md:my-6 overflow-x-auto md:overflow-visible">
                <!-- Overview / Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl flex items-center justify-center transition {{ request()->routeIs('dashboard') ? 'bg-white text-slate-900 shadow-md font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}"
                   title="Dashboard Overview">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </a>

                <!-- Consignments -->
                <a href="{{ route('shipments.index') }}" 
                   class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl flex items-center justify-center transition {{ request()->routeIs('shipments.*') ? 'bg-white text-slate-900 shadow-md font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}"
                   title="Consignments Manifests">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>

                <!-- Incident Desk (Exceptions) -->
                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                <a href="{{ route('exceptions.index') }}" 
                   class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl flex items-center justify-center transition relative {{ request()->routeIs('exceptions.*') ? 'bg-amber-400 text-slate-950 shadow-md font-bold' : 'hover:bg-slate-800/60 hover:text-amber-400 text-slate-400' }}"
                   title="Incident Desk">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-amber-400"></span>
                </a>
                @endif

                <!-- Carriers -->
                <a href="{{ route('carriers.index') }}" 
                   class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl flex items-center justify-center transition {{ request()->routeIs('carriers.*') ? 'bg-white text-slate-900 shadow-md font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}"
                   title="Partner Carriers">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </a>

                <!-- Hubs & Nodes -->
                <a href="{{ route('locations.index') }}" 
                   class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl flex items-center justify-center transition {{ request()->routeIs('locations.*') ? 'bg-white text-slate-900 shadow-md font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}"
                   title="Hubs &amp; Nodes">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>

                <!-- Audit Logs (Admin only) -->
                @if (auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('audit.index') }}" 
                   class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl flex items-center justify-center transition {{ request()->routeIs('audit.*') ? 'bg-white text-slate-900 shadow-md font-bold' : 'hover:bg-slate-800/60 hover:text-white text-slate-400' }}"
                   title="Audit Logs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </a>
                @endif
            </nav>

            <!-- Bottom Icons: Settings Modal & Logout -->
            <div class="flex md:flex-col items-center gap-2 sm:gap-3 mt-auto pt-2">
                
                <!-- Quick Settings Button (Theme & Language) -->
                <button type="button" onclick="openSettingsModal()" 
                        class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl hover:bg-slate-800/80 text-slate-400 hover:text-white flex items-center justify-center transition" 
                        title="Settings (Theme &amp; Language)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>

                @auth
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl hover:bg-red-500/20 text-slate-400 hover:text-red-400 flex items-center justify-center transition" title="Log Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
                @endauth
            </div>
        </aside>

        <!-- Right Main Panel Container (White/Dark Surface) -->
        <div class="flex-1 flex flex-col bg-[#F8FAFC] dark:bg-[#0B0F17] overflow-y-auto min-w-0 transition-colors duration-200">
            
            <!-- Top Navigation Bar inside Main Content (matching Image 2) -->
            <header class="p-4 sm:p-6 lg:px-8 border-b border-slate-200/80 dark:border-slate-800/80 bg-white/90 dark:bg-[#0B0F17]/90 backdrop-blur-md sticky top-0 z-20 flex flex-wrap items-center justify-between gap-4">
                
                <!-- Title & Brand Tagline -->
                <div class="flex items-center gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight" data-i18n="overview_title">
                            Overview
                        </h1>
                        <p class="text-xs text-slate-400 font-medium hidden sm:block" data-i18n="overview_subtitle">
                            Global shipment tracking, radar GIS corridors &amp; live telemetry
                        </p>
                    </div>
                </div>

                <!-- Right Utility Bar: Search, Quick Toggles, Notification, User Pill -->
                <div class="flex items-center gap-3">
                    
                    <!-- Search Icon / Quick Finder -->
                    <a href="{{ route('shipments.index') }}" class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition shadow-sm" title="Search Consignments">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </a>

                    <!-- Quick Language Toggle Pill -->
                    <button type="button" onclick="toggleLanguage()" id="quick-lang-btn" 
                            class="px-2.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 shadow-sm transition">
                        <span id="lang-flag">🇮🇩</span>
                        <span id="lang-label">ID</span>
                    </button>

                    <!-- Quick Theme Toggle Pill (Sun / Moon) -->
                    <button type="button" onclick="toggleTheme()" id="quick-theme-btn"
                            class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition shadow-sm"
                            title="Toggle Light / Dark Mode">
                        <span id="theme-icon">🌙</span>
                    </button>

                    @auth
                    <!-- Notifications Dropdown -->
                    <div class="relative">
                        @php
                            $unreadNotifications = auth()->user()->notifications()->where('is_read', false)->get();
                        @endphp
                        <button onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')" 
                                class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition shadow-sm relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if ($unreadNotifications->count() > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-500 text-white font-bold text-[9px] flex items-center justify-center animate-pulse">
                                    {{ $unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-[#121A28] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden">
                            <div class="p-3 bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200" data-i18n="alerts_title">Notifications</span>
                                @if ($unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.read-all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">Mark all read</button>
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
                                <div class="p-6 text-center text-xs text-slate-400">No new notifications.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Pill (matching Avatar in Image 2) -->
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-200 dark:border-slate-800">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-400 text-white font-extrabold text-xs flex items-center justify-center shadow-md">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                                {{ Str::limit(auth()->user()->name, 14) }}
                            </div>
                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded font-bold uppercase {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow transition">
                        Sign In
                    </a>
                    @endauth

                </div>
            </header>

            <!-- Global Flash Messages -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if (session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-medium shadow-sm mb-4">
                        <div class="flex items-center gap-2">
                            <span>✅</span>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400">&times;</button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 dark:bg-red-950/80 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-medium shadow-sm mb-4">
                        <div class="flex items-center gap-2">
                            <span>⚠️</span>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-600 dark:text-red-400">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Page Content View -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Modal: Settings (Theme Mode & Language Switcher) -->
    <div id="settings-modal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#121A28] border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⚙️</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" data-i18n="settings_title">System Settings</h3>
                </div>
                <button onclick="closeSettingsModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <!-- Theme Mode Selection -->
            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block" data-i18n="theme_label">
                    Appearance / Display Mode
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="setTheme('light')" id="btn-theme-light" 
                            class="p-4 rounded-2xl border-2 flex flex-col items-center gap-2 transition font-bold text-xs bg-slate-50 text-slate-800 border-emerald-500 shadow-sm">
                        <span class="text-2xl">☀️</span>
                        <span data-i18n="theme_light">Light Mode</span>
                        <span class="text-[10px] text-slate-400 font-normal">Crisp &amp; Clean</span>
                    </button>
                    <button type="button" onclick="setTheme('dark')" id="btn-theme-dark" 
                            class="p-4 rounded-2xl border-2 flex flex-col items-center gap-2 transition font-bold text-xs bg-slate-900 text-white border-transparent hover:border-slate-700 shadow-sm">
                        <span class="text-2xl">🌙</span>
                        <span data-i18n="theme_dark">Dark Mode</span>
                        <span class="text-[10px] text-slate-400 font-normal">Nautical Radar</span>
                    </button>
                </div>
            </div>

            <!-- Language Selection -->
            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block" data-i18n="language_label">
                    Language / Bahasa
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="setLanguage('en')" id="btn-lang-en" 
                            class="p-3.5 rounded-2xl border-2 flex items-center justify-center gap-2.5 transition font-bold text-xs bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white border-emerald-500">
                        <span class="text-xl">🇺🇸</span>
                        <span>English (EN)</span>
                    </button>
                    <button type="button" onclick="setLanguage('id')" id="btn-lang-id" 
                            class="p-3.5 rounded-2xl border-2 flex items-center justify-center gap-2.5 transition font-bold text-xs bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white border-transparent hover:border-slate-300">
                        <span class="text-xl">🇮🇩</span>
                        <span>Indonesia (ID)</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="button" onclick="closeSettingsModal()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition">
                    Done
                </button>
            </div>

        </div>
    </div>

    <!-- Multi-Language & Theme Controller Script -->
    <script>
        // Dictionary for Bilingual Support (English & Bahasa Indonesia)
        const translations = {
            en: {
                overview_title: "Overview",
                overview_subtitle: "Global shipment tracking, radar GIS corridors & live telemetry",
                settings_title: "System Settings",
                theme_label: "Appearance / Display Mode",
                theme_light: "Light Mode",
                theme_dark: "Dark Mode",
                language_label: "Language / Bahasa",
                alerts_title: "Notifications",
                next_port: "NEXT PORT BERTHING",
                eta_countdown: "ETA COUNTDOWN",
                active_delays: "ACTIVE DELAYS",
                all_consignments: "All Consignments",
                ocean_lines: "Ocean Lines",
                air_express: "Air Express",
                exceptions_delay: "Exceptions (Delay)",
                total_consignments: "Total Consignments",
                partner_carriers: "Partner Carriers",
                hubs_terminals: "Hubs & Terminals",
                ontime_reliability: "On-Time Reliability",
                active_manifests_title: "Active Consignment Manifests",
                active_manifests_sub: "Click any consignment row to zoom camera and track on the live map",
                tracking_id: "Tracking Identifier",
                mode: "Mode",
                consignee_carrier: "Consignee & Carrier",
                corridor_route: "Corridor Route",
                status: "Status",
                schedule_impact: "Schedule Impact",
                action: "Action",
                incident_notice: "Active Incident Notice",
                reefer_sensors: "Reefer Container Sensors",
                flight_telemetry: "Flight Telemetry (ADS-B)",
                details_btn: "Details",
            },
            id: {
                overview_title: "Ringkasan",
                overview_subtitle: "Pelacakan pengiriman global, koridor radar GIS & telemetri langsung",
                settings_title: "Pengaturan Sistem",
                theme_label: "Tampilan / Mode Layar",
                theme_light: "Mode Terang",
                theme_dark: "Mode Gelap",
                language_label: "Pilihan Bahasa",
                alerts_title: "Pemberitahuan",
                next_port: "PELABUHAN BERLABUH BERIKUTNYA",
                eta_countdown: "HITUNG MUNDUR ETA",
                active_delays: "KETERLAMBATAN AKTIF",
                all_consignments: "Semua Konsinyasi",
                ocean_lines: "Jalur Laut (Ocean)",
                air_express: "Kargo Udara (Air)",
                exceptions_delay: "Kendala & Gangguan",
                total_consignments: "Total Konsinyasi",
                partner_carriers: "Mitra Operator",
                hubs_terminals: "Hub & Terminal",
                ontime_reliability: "Keandalan Tepat Waktu",
                active_manifests_title: "Manifest Konsinyasi Aktif",
                active_manifests_sub: "Klik baris konsinyasi untuk mengarahkan peta ke rute perjalanan",
                tracking_id: "Nomor Pelacakan",
                mode: "Moda",
                consignee_carrier: "Penerima & Operator",
                corridor_route: "Rute Koridor",
                status: "Status",
                schedule_impact: "Dampak Jadwal",
                action: "Aksi",
                incident_notice: "Pemberitahuan Insiden Aktif",
                reefer_sensors: "Sensor Kontainer Reefer",
                flight_telemetry: "Telemetri Udara (ADS-B)",
                details_btn: "Rincian",
            }
        };

        let currentLang = localStorage.getItem('cargotrace_lang') || 'en';

        function applyLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('cargotrace_lang', lang);
            
            // Update UI elements with data-i18n
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    el.innerText = translations[lang][key];
                }
            });

            // Update Language pill
            const flagEl = document.getElementById('lang-flag');
            const labelEl = document.getElementById('lang-label');
            if (flagEl && labelEl) {
                flagEl.innerText = lang === 'id' ? '🇮🇩' : '🇺🇸';
                labelEl.innerText = lang.toUpperCase();
            }

            // Update modal buttons
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

        function toggleLanguage() {
            const nextLang = currentLang === 'en' ? 'id' : 'en';
            applyLanguage(nextLang);
        }

        function setLanguage(lang) {
            applyLanguage(lang);
        }

        function setTheme(theme) {
            localStorage.setItem('cargotrace_theme', theme);
            const root = document.documentElement;
            const iconEl = document.getElementById('theme-icon');
            const btnLight = document.getElementById('btn-theme-light');
            const btnDark = document.getElementById('btn-theme-dark');

            if (theme === 'dark') {
                root.classList.add('dark');
                root.classList.remove('light');
                if (iconEl) iconEl.innerText = '☀️';
                if (btnDark && btnLight) {
                    btnDark.classList.add('border-emerald-500');
                    btnDark.classList.remove('border-transparent');
                    btnLight.classList.remove('border-emerald-500');
                    btnLight.classList.add('border-transparent');
                }
            } else {
                root.classList.remove('dark');
                root.classList.add('light');
                if (iconEl) iconEl.innerText = '🌙';
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

        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            setTheme(isDark ? 'light' : 'dark');
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
