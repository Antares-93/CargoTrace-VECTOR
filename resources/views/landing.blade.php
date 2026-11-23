<!DOCTYPE html>
<html lang="en" id="html-root" class="light" style="scroll-behavior: smooth;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vector • Global Cargo Visibility &amp; Intelligent Tracking</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    

    <!-- Tailwind CSS -->
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
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased selection:bg-emerald-500 selection:text-white min-h-screen flex flex-col">

    <!-- Top Navigation Header -->
    <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex items-center justify-between sticky top-0 bg-[#F8FAFC]/95 backdrop-blur-md z-40 border-b border-slate-200/60">
        
        <!-- Logo (Freestanding Vector Logo) -->
        <a href="/" class="flex items-center gap-3 group">
            <img src="/images/vector-logo-icon.png" alt="Vector Logo" class="w-9 h-9 object-contain group-hover:scale-105 transition transform">
            <div class="flex flex-col">
                <span class="text-xl font-extrabold tracking-tight text-slate-900 flex items-center gap-1.5">
                    VECTOR <span class="text-emerald-700 text-xs px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 uppercase font-mono font-bold tracking-wider">CARGOTRACE</span>
                </span>
                <span class="text-[10px] text-slate-400 font-medium tracking-wide">Global Shipment Visibility Platform</span>
            </div>
        </a>

        <!-- Middle Navigation Links (Fully Clickable with Smooth Scroll) -->
        <nav class="hidden md:flex items-center gap-8 text-xs font-bold text-slate-600">
            <a href="#about" class="hover:text-emerald-600 transition">About</a>
            <a href="#corridors" class="hover:text-emerald-600 transition">Corridors</a>
            <a href="#features" class="hover:text-emerald-600 transition">Fitur</a>
            <a href="#tracking" class="hover:text-emerald-600 transition">Tracking</a>
            <a href="{{ route('dashboard') }}" class="hover:text-emerald-600 transition flex items-center gap-1.5"><span>Control Room</span><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
        </nav>

        <!-- Right Action Buttons -->
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-full shadow-sm hover:shadow transition flex items-center gap-2">
                    <span>Control Room</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @else
                <a href="{{ route('register') }}" class="text-xs font-bold text-slate-700 hover:text-emerald-600 transition px-3 py-2">
                    Sign Up
                </a>
                <a href="{{ route('login') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-full shadow-sm hover:shadow transition flex items-center gap-2">
                    <span>Sign In</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16 sm:space-y-24 py-4 sm:py-8">

        <!-- 1. HERO & ABOUT SECTION (Vector Emerald Palette) -->
        <section id="about" class="relative bg-gradient-to-br from-[#DCFCE7]/70 via-[#F0FDF4] to-[#E2E8F0]/40 rounded-[2.5rem] p-6 sm:p-10 lg:p-14 overflow-hidden border border-emerald-100 shadow-sm scroll-mt-24">
            
            <!-- Background Decorative Wave -->
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-emerald-200/40 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-green-100/50 blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
                
                <!-- Left Hero Content -->
                <div class="lg:col-span-6 space-y-6">
                    
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/95 border border-emerald-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span class="text-[11px] font-bold text-emerald-900 tracking-wider uppercase font-mono">
                            GLOBAL MULTIMODAL VISIBILITY
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                        Experience The Magic Of Global Cargo Visibility!
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl">
                        Monitor container ships and air freight across international maritime corridors and skyways with sub-hour AIS/ADS-B telemetry, automated ETA recalculation, and predictive delay intelligence.
                    </p>

                    <!-- Instant Waybill Tracking Search Box -->
                    <div id="tracking" class="pt-2 scroll-mt-28">
                        <form action="{{ route('shipments.index') }}" method="GET" class="bg-white p-2 rounded-2xl shadow-md border border-slate-200 flex flex-col sm:flex-row items-center gap-2 max-w-lg">
                            <div class="flex items-center gap-3 px-3 py-2 w-full">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" name="search" placeholder="Enter Waybill, B/L, or AWB (e.g. AT-2026-B8910)" 
                                       class="w-full text-xs font-mono font-medium text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none">
                            </div>
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition shrink-0 flex items-center justify-center gap-2">
                                <span>Track Consignment</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                        <div class="flex items-center gap-4 mt-2.5 px-2 text-[11px] text-slate-500 font-mono">
                            <span>Popular tracks:</span>
                            <a href="{{ route('shipments.index', ['search' => 'AT-2026-B8910']) }}" class="text-emerald-600 hover:underline font-semibold">AT-2026-B8910</a>
                            <span>•</span>
                            <a href="{{ route('shipments.index', ['search' => 'AT-2026-A1042']) }}" class="text-emerald-600 hover:underline font-semibold">AT-2026-A1042</a>
                        </div>
                    </div>

                    <!-- Fast Feature Pills -->
                    <div class="flex flex-wrap items-center gap-3 pt-2 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-1.5 bg-white/80 px-3 py-1.5 rounded-xl border border-slate-200/80 shadow-2xs">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Live OpenStreetMap Radar</span>
                        </div>
                        <div class="flex items-center gap-1.5 bg-white/80 px-3 py-1.5 rounded-xl border border-slate-200/80 shadow-2xs">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Audit &amp; Compliance Trail</span>
                        </div>
                        <div class="flex items-center gap-1.5 bg-white/80 px-3 py-1.5 rounded-xl border border-slate-200/80 shadow-2xs">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>RESTful Sanctum API</span>
                        </div>
                    </div>

                </div>

                <!-- Right Hero Imagery (Large Container Cargo Ship) -->
                <div class="lg:col-span-6 relative">
                    
                    <!-- Main Large Container Cargo Ship Image Card -->
                    <div class="relative w-full h-72 sm:h-96 rounded-3xl overflow-hidden shadow-xl border-4 border-white group">
                        <img src="/images/hero-cargo-ship.jpg" alt="Large Container Cargo Ship" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-mono tracking-wider text-emerald-300 block font-bold">Ocean Freight Lead • ULCV</span>
                                <span class="text-sm font-extrabold">Maersk Mc-Kinney Møller • 18,270 TEU</span>
                            </div>
                            <span class="px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-lg text-xs font-mono font-bold">21.4 KTS</span>
                        </div>
                    </div>

                    <!-- Floating Secondary Container Ship Card -->
                    <div class="sm:absolute -bottom-8 -left-8 sm:w-72 bg-white p-3 rounded-2xl shadow-2xl border border-slate-100 mt-4 sm:mt-0 flex items-center gap-3">
                        <img src="/images/hero-ship.jpg" alt="Container Vessel Port" class="w-16 h-16 rounded-xl object-cover shrink-0">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider font-mono">Ocean Lead</span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 truncate">Maersk Mc-Kinney</h4>
                            <p class="text-[11px] text-slate-500 font-mono">31,900 KG • In Transit</p>
                        </div>
                    </div>

                    <!-- Floating Pill Tag -->
                    <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-slate-200 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-extrabold text-slate-800 font-mono">AIS + ADS-B ACTIVE</span>
                    </div>

                </div>

            </div>

        </section>

        <!-- 2. POPULAR STRATEGIC CORRIDORS -->
        <section id="corridors" class="space-y-6 scroll-mt-24">
            
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Strategic Global Corridors
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                        High-density multimodal lanes connecting Southeast Asia to European &amp; American trade hubs.
                    </p>
                </div>
                <a href="{{ route('locations.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5">
                    <span>View all terminal nodes</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Corridor 1: Tanjung Priok -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col group">
                    <div class="relative h-48 overflow-hidden">
                        <img src="/images/corridor-jakarta.jpg" alt="Port of Tanjung Priok" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-slate-900/80 text-white font-mono text-[10px] font-bold backdrop-blur-sm">
                            IDTPP
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 font-mono">Origin Terminal</div>
                            <h3 class="text-base font-extrabold text-slate-900">Port of Tanjung Priok</h3>
                            <p class="text-xs text-slate-500 mt-1">Jakarta Hub • 4,200 TEU automated container handling capacity daily.</p>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                            <span class="text-slate-500 font-mono">Sea Corridor Origin</span>
                            <a href="{{ route('shipments.index') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl transition">
                                Track Lane &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Corridor 2: Singapore Transshipment Hub -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col group">
                    <div class="relative h-48 overflow-hidden">
                        <img src="/images/corridor-singapore.jpg" alt="Singapore Port & Airport" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-slate-900/80 text-white font-mono text-[10px] font-bold backdrop-blur-sm">
                            SGSIN
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 font-mono">Transshipment Node</div>
                            <h3 class="text-base font-extrabold text-slate-900">Port of Singapore (PSA)</h3>
                            <p class="text-xs text-slate-500 mt-1">Changi &amp; Pasir Panjang • Multimodal ocean-to-air cross-docking junction.</p>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                            <span class="text-slate-500 font-mono">Sea + Air Gateway</span>
                            <a href="{{ route('shipments.index') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl transition">
                                Track Lane &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Corridor 3: Port of Rotterdam -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col group">
                    <div class="relative h-48 overflow-hidden">
                        <img src="/images/corridor-rotterdam.jpg" alt="Port of Rotterdam" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-slate-900/80 text-white font-mono text-[10px] font-bold backdrop-blur-sm">
                            NLRTM
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 font-mono">Destination Europort</div>
                            <h3 class="text-base font-extrabold text-slate-900">Port of Rotterdam</h3>
                            <p class="text-xs text-slate-500 mt-1">Europort Terminal 4 • Autonomous berthing &amp; European hinterland rail connectivity.</p>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                            <span class="text-slate-500 font-mono">European Terminal</span>
                            <a href="{{ route('shipments.index') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl transition">
                                Track Lane &rarr;
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </section>

        <!-- 4. LOGISTICS PROCESS MADE SIMPLE -->
        <section id="features" class="space-y-8 py-4 scroll-mt-24">
            
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Global Cargo Visibility Made Simple!
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Transparent, milestone-validated consignment tracking from loading dock to final consignee signature.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Step 1 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase font-mono text-slate-400">Step 01</span>
                            <h3 class="text-base font-extrabold text-slate-900">Waybill &amp; Manifest Booking</h3>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Automated consignment issuance with standard readable waybills, itemized cargo manifests, and cold chain temperature thresholds.
                            </p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 text-xs font-bold text-emerald-600">
                        Immutable Audit Logging &rarr;
                    </div>
                </div>

                <!-- Step 2 (Highlighted Emerald card) -->
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 text-white rounded-3xl p-6 shadow-xl flex flex-col justify-between transform md:-translate-y-2">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase font-mono text-emerald-200">Step 02 • Core Feature</span>
                            <h3 class="text-base font-extrabold text-white">Live GIS Radar &amp; Telemetry</h3>
                            <p class="text-xs text-emerald-100 mt-2 leading-relaxed">
                                Interactive OpenStreetMap radar tracking with maritime AIS coordinates, air ADS-B altitude &amp; speed, and voyage milestone progress.
                            </p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/20">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-white bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl transition">
                            <span>Open Radar Control Room</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase font-mono text-slate-400">Step 03</span>
                            <h3 class="text-base font-extrabold text-slate-900">Incident Desk &amp; Resolution</h3>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Automated exception classification (Customs hold, weather delay, port congestion) with real-time in-app alerts and timeline synchronization.
                            </p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 text-xs font-bold text-emerald-600">
                        Zero Downtime Incident Desk &rarr;
                    </div>
                </div>

            </div>

        </section>

        <!-- 5. CALL TO ACTION BANNER -->
        <section class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white rounded-[2.5rem] p-8 sm:p-12 lg:p-16 shadow-xl relative overflow-hidden">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                
                <div class="lg:col-span-7 space-y-4">
                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 rounded-full font-mono text-[10px] font-bold tracking-widest uppercase">
                        UNIFIED SUPPLY CHAIN VISIBILITY
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                        UNLEASH GLOBAL LOGISTICS PRECISION WITH VECTOR
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl">
                        Built for modern supply chain operators, ocean lines, and air cargo forwarders. Experience the live control room with zero configuration.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-4">
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow transition flex items-center gap-2">
                            <span>Launch Control Room</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                            Create Account
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/15 text-xs font-mono space-y-3 w-full max-w-sm shadow-2xl">
                        <div class="flex items-center justify-between border-b border-white/10 pb-2">
                            <span class="text-slate-400">TELEMETRY ENGINE</span>
                            <span class="text-emerald-400 font-bold">ONLINE 100%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-300">Monitored Vessels</span>
                            <span class="font-bold text-white">9 Maritime Carriers</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-300">Air Express Flights</span>
                            <span class="font-bold text-white">3 Daily Linehauls</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-300">Average ETA Deviation</span>
                            <span class="font-bold text-emerald-300">&lt; 1.2 Hours</span>
                        </div>
                        <div class="pt-2 border-t border-white/10 text-[10px] text-slate-400">
                            Integrated with Leaflet.js &amp; OpenStreetMap tiles
                        </div>
                    </div>
                </div>

            </div>

        </section>

        <!-- 6. PARTNER ALLIANCES RIBBON (Placed at bottom, right above footer) -->
        <section class="py-6 border-t border-slate-200/80">
            @include('partials.carrier-alliances')
        </section>

    </main>

    <!-- Footer with (C) Copyright Circle -->
    <footer class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
        <div class="flex items-center gap-2">
            <img src="/images/vector-logo-icon.png" alt="Vector Logo" class="w-5 h-5 object-contain">
            <span class="font-bold text-slate-700">Vector CargoTrace &copy; 2026.</span>
            <span>Global Shipment Visibility &amp; Radar Tracking.</span>
        </div>
        <div class="flex items-center gap-4 text-xs font-medium text-slate-600">
            <a href="{{ route('dashboard') }}" class="hover:text-emerald-600">Control Room</a>
            <a href="{{ route('shipments.index') }}" class="hover:text-emerald-600">Consignments</a>
            <a href="{{ route('login') }}" class="hover:text-emerald-600">Sign In</a>
        </div>
    </footer>

    </body>
</html>
