@extends('layouts.app')

@section('content')
<div class="space-y-10 sm:space-y-12">

    <!-- ==========================================
         1. HERO SECTION (1-to-1 Mirror of Landing Page Hero Layout in Emerald Green)
         Left (6 cols): Headline, Description, Waybill Search, Next Port Berthing Countdown
         Right (6 cols): Live OpenStreetMap Radar framed in rounded-3xl border-4 border-white
         ========================================== -->
    <section class="relative bg-gradient-to-br from-[#DCFCE7]/70 via-[#F0FDF4] to-[#E2E8F0]/40 rounded-[2.5rem] p-6 sm:p-10 lg:p-12 overflow-hidden border border-emerald-100 shadow-sm">
        
        <!-- Subtle decorative background blurs (matching landing page in emerald green) -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-emerald-200/40 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-green-100/50 blur-3xl pointer-events-none"></div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center relative z-10">
            
            <!-- Left Hero Content (6 cols) -->
            <div class="lg:col-span-6 space-y-6">
                
                <!-- Live Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/95 border border-emerald-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-[11px] font-bold text-emerald-900 tracking-wider uppercase font-mono" data-i18n="hero_pill">
                        RADAR LANGSUNG &amp; VISIBILITAS ARMADA
                    </span>
                </div>

                <!-- Main Headline -->
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-[1.2]" data-i18n="hero_headline">
                    Pantau Koridor Multimodal &amp; Armada Real-Time
                </h1>

                <!-- Subtitle -->
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-xl" data-i18n="hero_desc">
                    Pantau kapal kontainer dan kargo udara melintasi koridor laut internasional dan jalur penerbangan dengan telemetri AIS/ADS-B cepat, kalkulasi otomatis ETA, dan peringatan keterlambatan prediktif.
                </p>

                <!-- Instant Waybill Tracking Search Box (Matching Landing Page) -->
                <div>
                    <form action="{{ route('shipments.index') }}" method="GET" class="bg-white p-2 rounded-2xl shadow-md border border-slate-200 flex flex-col sm:flex-row items-center gap-2 max-w-xl">
                        <div class="flex items-center gap-2.5 px-3 py-2 w-full">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Masukkan Nomor Resi, B/L, atau AWB (misal AT-2026-B8910)" 
                                   data-i18n-placeholder="hero_search_placeholder"
                                   class="w-full text-xs font-mono font-medium text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none">
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition shrink-0 flex items-center justify-center gap-2">
                            <span data-i18n="hero_track_btn">Lacak Konsinyasi</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                    <div class="flex items-center gap-3 mt-2 px-2 text-[11px] text-slate-500 font-mono">
                        <span data-i18n="popular_tracks_label">Resi populer:</span>
                        <a href="{{ route('shipments.index', ['search' => 'AT-2026-B8910']) }}" class="text-emerald-600 hover:underline font-semibold">AT-2026-B8910</a>
                        <span>•</span>
                        <a href="{{ route('shipments.index', ['search' => 'AT-2026-A1042']) }}" class="text-emerald-600 hover:underline font-semibold">AT-2026-A1042</a>
                    </div>
                </div>

                <!-- Next Port Berthing Telemetry Card -->
                <div class="bg-white/80 backdrop-blur-md border border-emerald-200/90 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-500" data-i18n="next_port">PELABUHAN BERLABUH BERIKUTNYA</span>
                        </div>
                        <div class="text-xs sm:text-sm font-extrabold text-slate-900">
                            @if ($metrics['next_berthing'])
                                <span>{{ $metrics['next_berthing']->destination->name }} ({{ $metrics['next_berthing']->destination->code }})</span>
                                <span class="text-slate-500 font-normal hidden sm:inline">• {{ $metrics['next_berthing']->berth_number ?? 'Berth 402' }}</span>
                            @else
                                <span>Port of Los Angeles (USLAX)</span>
                                <span class="text-slate-500 font-normal hidden sm:inline">• Pier 400 (Terminal 3)</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 font-mono text-xs shrink-0">
                        <div class="bg-slate-900 text-white px-3 py-1.5 rounded-xl shadow-xs">
                            <span class="text-[9px] text-slate-400 block uppercase" data-i18n="eta_countdown">HITUNG MUNDUR ETA</span>
                            <span id="berthing-countdown" class="text-emerald-400 font-extrabold text-xs">15h 42m 19s</span>
                        </div>
                        <div class="bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600">
                            <span class="text-[9px] text-slate-400 block uppercase" data-i18n="scheduled_label">TERJADWAL:</span>
                            <span class="font-bold text-slate-800">
                                {{ $metrics['next_berthing'] ? $metrics['next_berthing']->expected_arrival->format('d M Y') : '04 Nov 2026' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Hero Interactive Map Frame (6 cols) -->
            <div class="lg:col-span-6 flex flex-col justify-between">
                
                @if(auth()->check() && auth()->user()->isCustomer())
                <!-- Customer Consignment Radar Search Header -->
                <div class="mb-3 bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2.5 shadow-xs">
                    <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                        <div class="relative w-full">
                            <svg class="w-4 h-4 text-emerald-600 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="focus" value="{{ request('focus') }}" placeholder="Masukkan Nomor Resi, B/L, atau AWB (misal AT-2026-B8910)..." class="w-full pl-9 pr-3 py-1.5 text-xs font-mono rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shrink-0">
                            <span>Lacak Rute</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                    @if($metrics['shipments']->count() > 0)
                    <div class="flex items-center gap-2 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[10px] font-mono text-slate-500 overflow-x-auto">
                        <span class="shrink-0">Pilih Konsinyasi Anda:</span>
                        @foreach($metrics['shipments']->take(3) as $cs)
                        <a href="{{ route('dashboard', ['focus' => $cs->shipment_number]) }}" class="px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:underline font-bold shrink-0">
                            {{ $cs->shipment_number }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

                <div class="relative w-full h-[380px] sm:h-[420px] rounded-3xl overflow-hidden shadow-xl border-4 border-white bg-slate-100">
                    
                    <!-- Leaflet OpenStreetMap Container -->
                    <div id="radar-map" class="w-full h-full z-10"></div>

                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                    <!-- Admin & Operator Full Command Telemetry Overlays -->
                    <div class="absolute top-4 left-4 z-[500] inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/90 text-white backdrop-blur-md border border-white/20 shadow-md font-mono text-[10px] font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span data-i18n="telemetry_active_badge">AKSES PENUH ARMADA • AIS &amp; ADS-B AKTIF</span>
                    </div>

                    <div class="absolute top-4 right-4 z-[500] flex items-center gap-1.5 font-mono text-[10px] font-bold">
                        <span class="px-2.5 py-1 rounded-full bg-white/95 text-emerald-800 shadow-md border border-slate-200" data-i18n="air_speed_tag">
                            UDARA: 485 KTS
                        </span>
                        <span class="px-2.5 py-1 rounded-full bg-amber-500 text-white shadow-md" data-i18n="sea_delay_tag">
                            TUNDA LAUT: +28J
                        </span>
                    </div>

                    <div class="absolute bottom-4 left-4 z-[500] bg-white/95 backdrop-blur-md border border-slate-200 rounded-2xl p-3 shadow-xl max-w-[260px] hidden sm:block">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-mono font-bold text-slate-500 uppercase">KAPAL UTAMA (LEAD)</span>
                        </div>
                        <div class="text-xs font-extrabold text-slate-900 mt-0.5">Maersk Mc-Kinney Moller</div>
                        <div class="text-[10px] text-slate-500 font-mono mt-0.5 flex items-center justify-between">
                            <span>AT-2026-B8910</span>
                            <span class="text-amber-600 font-bold">+28h Delay</span>
                        </div>
                    </div>

                    <div class="absolute bottom-4 right-4 z-[500] bg-white/95 backdrop-blur-md border border-slate-200 rounded-2xl p-2.5 text-[9px] font-mono space-y-1 shadow-lg hidden md:block">
                        <div class="font-bold text-slate-400 uppercase tracking-wider mb-0.5" data-i18n="modes_legend">LEGENDA MODA</div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-1 bg-emerald-600 rounded"></span>
                            <span class="text-slate-700 font-semibold" data-i18n="legend_ocean">Kapal Laut (Jakarta &rarr; Suez)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-0.5 border-t border-dashed border-amber-500"></span>
                            <span class="text-slate-500" data-i18n="legend_remaining">Sisa Rute (Suez &rarr; RTM)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-1 bg-emerald-400 rounded"></span>
                            <span class="text-slate-700 font-semibold" data-i18n="legend_air">Kargo Udara (SIN &rarr; FRA)</span>
                        </div>
                    </div>
                    @else
                        <!-- Customer Role Map Overlays -->
                        @if($leadShipment)
                        <div class="absolute top-4 left-4 z-[500] inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/90 text-white backdrop-blur-md border border-white/20 shadow-md font-mono text-[10px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>RUTE KONSINYASI: {{ $leadShipment->shipment_number }}</span>
                        </div>

                        <div class="absolute bottom-4 left-4 z-[500] bg-white/95 backdrop-blur-md border border-slate-200 rounded-2xl p-3 shadow-xl max-w-[280px]">
                            <div class="flex items-center justify-between text-[10px] font-mono font-bold text-emerald-700 uppercase">
                                <span>{{ $leadShipment->transport_mode }} FREIGHT</span>
                                <span>{{ $leadShipment->status }}</span>
                            </div>
                            <div class="text-xs font-extrabold text-slate-900 mt-1">
                                {{ $leadShipment->origin->name }} &rarr; {{ $leadShipment->destination->name }}
                            </div>
                            <div class="text-[10px] text-slate-500 font-mono mt-1">
                                Operator: {{ $leadShipment->carrier->name }}
                            </div>
                        </div>
                        @else
                        <!-- Customer Prompt Overlay (Before entering tracking number) -->
                        <div class="absolute inset-0 z-[450] bg-slate-900/30 backdrop-blur-[2px] flex items-center justify-center p-6 text-center pointer-events-none">
                            <div class="bg-white/95 dark:bg-[#121A28]/95 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-sm space-y-2 pointer-events-auto">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 text-emerald-600 mx-auto flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Peta Radar Konsinyasi</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Masukkan nomor resi, B/L, atau AWB pada kolom di atas, atau klik salah satu konsinyasi Anda pada tabel di bawah untuk menampilkan rute navigasi dan telemetri armada secara langsung.
                                </p>
                            </div>
                        </div>
                        @endif
                    @endif

                </div>
            </div>

        </div>
    </section>

    <!-- ==========================================
         2. PRIMARY 4 KPI CARDS (Matching "Popular Strategic Corridors" Cards on Landing Page)
         ========================================== -->
    <section class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Card 1: Total Consignments -->
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-mono" data-i18n="total_consignments">
                        Total Konsinyasi
                    </span>
                    <span class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="my-4">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $metrics['total_consignments'] }} B/L &amp; AWB
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1" data-i18n="active_global_corridors">
                        Koridor global aktif
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs">
                    <span class="text-slate-400 font-mono"><span data-i18n="in_transit_sub">{{ $metrics['in_transit_count'] }} dalam perjalanan</span></span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50 font-bold font-mono text-[10px]" data-i18n="percent_active">
                        +100% aktif
                    </span>
                </div>
            </div>

            <!-- Card 2: Air Express Fleet -->
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-mono" data-i18n="air_express">
                        Jalur Kargo Udara
                    </span>
                    <span class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
                    </span>
                </div>
                <div class="my-4">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight" data-i18n="flights_count">
                        3 Penerbangan
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1" data-i18n="high_priority_express">
                        Ekspres prioritas tinggi
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs">
                    <span class="text-slate-400 font-mono" data-i18n="speed_kts">Kecepatan 485 Kts</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50 font-bold font-mono text-[10px]">
                        +0.14%
                    </span>
                </div>
            </div>

            <!-- Card 3: Ocean Lines Fleet -->
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-mono" data-i18n="ocean_lines">
                        Armada Jalur Laut
                    </span>
                    <span class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M1 16l3.5 5h14.5l3-5H1z"/>
                            <rect x="4" y="11" width="3" height="4" rx="0.5"/>
                            <rect x="8" y="11" width="3" height="4" rx="0.5"/>
                            <rect x="12" y="11" width="3" height="4" rx="0.5"/>
                            <rect x="6" y="7.5" width="3" height="3" rx="0.5"/>
                            <rect x="10" y="7.5" width="3" height="3" rx="0.5"/>
                            <path d="M16.5 9h3.5v6h-3.5V9zm.5-3h1.5v2H17V6z"/>
                        </svg>
                    </span>
                </div>
                <div class="my-4">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight" data-i18n="vessels_count">
                        9 Kapal
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1"><span data-i18n="partner_carriers_sub">{{ $metrics['carrier_count'] }} Mitra operator</span></div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs">
                    <span class="text-slate-400 font-mono" data-i18n="carriers_list">Maersk, EVG, MSC</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50 font-bold font-mono text-[10px]">
                        +0.31%
                    </span>
                </div>
            </div>

            <!-- Card 4: On-Time Reliability -->
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-mono" data-i18n="ontime_reliability">
                        Keandalan Tepat Waktu
                    </span>
                    <span class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="my-4">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $metrics['on_time_reliability'] }}%
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1" data-i18n="multimodal_network_rate">
                        Tingkat jaringan multimodal
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs">
                    <span class="text-slate-400 font-mono" data-i18n="benchmark_sub">Standar Acuan: 90%</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50 font-bold font-mono text-[10px]" data-i18n="optimal_label">
                        Optimal
                    </span>
                </div>
            </div>

        </div>
    </section>


    <!-- ==========================================
         4. 3-CARD SECTION (Matching "Journey To The Skies Made Simple!" with Center Emerald Highlight)
         Card 1: Reefer IoT Sensor
         Card 2: Center Highlighted Incident Desk Alert (Vector Emerald Accent)
         Card 3: ADS-B Flight Telemetry
         ========================================== -->
    <section class="space-y-6">
        
        <div class="text-center max-w-xl mx-auto space-y-1.5">
            <span class="text-xs font-mono font-bold tracking-wider uppercase text-emerald-600 dark:text-emerald-400" data-i18n="feature_subheading">
                VISIBILITAS KORIDOR LAUT &amp; UDARA TERPADU
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight" data-i18n="feature_heading">
                Intelijen Multimodal Terkoneksi Penuh
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
            
            <!-- Card 1: Cold Chain Reefer IoT Sensor -->
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300 uppercase flex items-center gap-2" data-i18n="reefer_sensors">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Sensor Kontainer Reefer
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/50 text-[10px] font-mono font-bold" data-i18n="status_normal">
                            Normal
                        </span>
                    </div>

                    <div class="space-y-3 font-mono">
                        <div class="bg-slate-50 dark:bg-[#070A10] border border-slate-100 dark:border-slate-800 rounded-2xl p-4">
                            <span class="text-[10px] text-slate-400 uppercase block mb-1" data-i18n="sensor_temp">Suhu Kontainer</span>
                            <div class="text-3xl font-extrabold text-slate-900 dark:text-white">4.2°C</div>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold" data-i18n="sensor_temp_sub">±0.2°C Stabil</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-[#070A10] border border-slate-100 dark:border-slate-800 rounded-2xl p-4">
                            <span class="text-[10px] text-slate-400 uppercase block mb-1" data-i18n="sensor_humidity">Kelembapan</span>
                            <div class="text-3xl font-extrabold text-slate-900 dark:text-white">62%</div>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold" data-i18n="sensor_humidity_sub">Optimal</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 font-mono">
                    Node ID: RF-9021-SUEZ • Bat: 94%
                </div>
            </div>

            <!-- Card 2: CENTER EMERALD HIGHLIGHTED CARD (Incident Desk Alert) -->
            <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-slate-900 text-white rounded-3xl p-6 sm:p-7 shadow-xl relative overflow-hidden flex flex-col justify-between transform md:-translate-y-2">
                <div class="absolute -right-8 -bottom-8 w-40 h-40 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="flex items-center gap-2 text-xs font-mono font-bold text-amber-300 uppercase" data-i18n="incident_notice">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            Pemberitahuan Insiden Aktif
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white border border-white/30 text-[10px] font-mono font-bold uppercase" data-i18n="severity_high">
                            Tinggi
                        </span>
                    </div>

                    <h3 class="text-xl font-extrabold text-white tracking-tight mt-2" data-i18n="incident_title">
                        Keterlambatan Konvoi Terusan Suez
                    </h3>
                    <p class="text-xs text-emerald-100 mt-3 leading-relaxed" data-i18n="incident_desc">
                        Antrean kongesti di pintu masuk Terusan Suez. Kapal tertunda +28 jam. Penerbangan udara QR-803F beroperasi normal.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-white/20 flex items-center justify-between text-xs font-mono">
                    <span class="text-emerald-200" data-i18n="detected_ago">Terdeteksi 9 jam lalu</span>
                    @if (auth()->check() && (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator())))
                    <a href="{{ route('exceptions.index') }}" class="px-4 py-2 bg-white text-slate-900 font-bold rounded-xl shadow hover:bg-emerald-50 transition text-xs flex items-center gap-1.5">
                        <span data-i18n="view_desk">Buka Meja Insiden</span> &rarr;
                    </a>
                    @else
                    <span class="text-amber-300 font-bold" data-i18n="investigating_label">Dalam Investigasi</span>
                    @endif
                </div>
            </div>

            <!-- Card 3: ADS-B Flight Telemetry -->
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300 uppercase flex items-center gap-1.5" data-i18n="flight_telemetry">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Telemetri Penerbangan (ADS-B)
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] font-mono font-bold">
                            QR-772-91024
                        </span>
                    </div>

                    <div class="space-y-3 font-mono">
                        <div class="bg-slate-50 dark:bg-[#070A10] border border-slate-100 dark:border-slate-800 rounded-2xl p-4">
                            <span class="text-[10px] text-slate-400 uppercase block mb-1" data-i18n="flight_craft">Penerbangan / Pesawat</span>
                            <div class="text-lg font-extrabold text-slate-900 dark:text-white">QR-803F</div>
                            <span class="text-[11px] text-slate-500">Boeing 777-200F Cargo</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-[#070A10] border border-slate-100 dark:border-slate-800 rounded-2xl p-4">
                            <span class="text-[10px] text-slate-400 uppercase block mb-1" data-i18n="flight_speed_alt">Kecepatan Udara &amp; Ketinggian</span>
                            <div class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">485 Kts</div>
                            <span class="text-[11px] text-slate-500">FL360 (36.000 ft Cruising)</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 font-mono">
                    Airway L888 • ETA: 21:30 UTC
                </div>
            </div>

        </div>
    </section>


    <!-- ==========================================
         5. VOYAGE MILESTONES (Lead Ocean Transit Progress)
         ========================================== -->
    <section class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-sm">
        <div class="flex items-center justify-between text-xs mb-3">
            <span class="font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[11px] font-mono" data-i18n="voyage_milestones">
                Tahapan Pelayaran (Jalur Laut Utama)
            </span>
            <span class="font-mono text-emerald-600 dark:text-emerald-400 text-xs font-bold" data-i18n="stage_active_sub">
                Tahap 3 dari 4 Aktif
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
            <!-- Stage 1 -->
            <div class="bg-slate-50 dark:bg-[#162235] border border-slate-200/80 dark:border-[#233550] rounded-2xl p-3.5">
                <span class="text-[9px] text-slate-400 uppercase font-mono block" data-i18n="stage_1">Tahap 1</span>
                <span class="text-xs font-bold text-slate-800 dark:text-white block mt-0.5">{{ $leadShipment->origin->name ?? 'Tanjung Priok' }}</span>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-semibold" data-i18n="status_departed">&#10003; Berangkat</span>
            </div>

            <!-- Stage 2 -->
            <div class="bg-slate-50 dark:bg-[#162235] border border-slate-200/80 dark:border-[#233550] rounded-2xl p-3.5">
                <span class="text-[9px] text-slate-400 uppercase font-mono block" data-i18n="stage_2">Tahap 2</span>
                <span class="text-xs font-bold text-slate-800 dark:text-white block mt-0.5">Singapore Hub</span>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-semibold" data-i18n="status_cleared">&#10003; Lolos Izin</span>
            </div>

            <!-- Stage 3 (Active) -->
            <div class="bg-emerald-50 dark:bg-emerald-950/60 border-2 border-emerald-500 rounded-2xl p-3.5 shadow-sm">
                <span class="text-[9px] text-emerald-700 dark:text-emerald-300 uppercase font-mono font-bold block" data-i18n="stage_3">Tahap 3</span>
                <span class="text-xs font-bold text-slate-900 dark:text-white block mt-0.5">Suez Canal</span>
                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-mono font-bold" data-i18n="status_anchored">Lego Jangkar (Antre)</span>
            </div>

            <!-- Stage 4 -->
            <div class="bg-slate-50 dark:bg-[#162235] border border-slate-200/80 dark:border-[#233550] rounded-2xl p-3.5 opacity-60">
                <span class="text-[9px] text-slate-400 uppercase font-mono block" data-i18n="stage_4">Tahap 4</span>
                <span class="text-xs font-bold text-slate-800 dark:text-white block mt-0.5">{{ $leadShipment->destination->name ?? 'Rotterdam Hub' }}</span>
                <span class="text-[10px] text-slate-400 font-mono" data-i18n="status_scheduled">Terjadwal</span>
            </div>
        </div>
    </section>


    <!-- ==========================================
         6. ACTIVE CONSIGNMENT MANIFESTS TABLE (Clean Modern Table in Emerald Accent)
         ========================================== -->
    <section id="manifests-section" class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-[2.5rem] p-6 sm:p-8 shadow-sm scroll-mt-24">
        
        <!-- Table Top Bar: Title & Filter Pills -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight" data-i18n="active_manifests_title">
                    Daftar Manifest Konsinyasi Aktif
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1" data-i18n="active_manifests_sub">
                    Klik baris konsinyasi untuk mengarahkan kamera dan melacak pada peta langsung
                </p>
            </div>

            <!-- Filter Pills & Search -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-[#070A10] p-1.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-x-auto text-xs">
                    <a href="{{ route('dashboard', ['filter' => 'all', 'search' => request('search')]) }}#manifests-section" data-preserve-scroll 
                       class="px-4 py-2 rounded-xl font-bold transition whitespace-nowrap {{ ($filters['filter'] ?? 'all') === 'all' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}" data-i18n="all_consignments">
                        Semua Konsinyasi
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'ocean', 'search' => request('search')]) }}#manifests-section" data-preserve-scroll 
                       class="px-4 py-2 rounded-xl font-bold transition whitespace-nowrap {{ ($filters['filter'] ?? '') === 'ocean' ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}" data-i18n="ocean_lines_tab">
                        Jalur Laut (Ocean)
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'air', 'search' => request('search')]) }}#manifests-section" data-preserve-scroll 
                       class="px-4 py-2 rounded-xl font-bold transition whitespace-nowrap {{ ($filters['filter'] ?? '') === 'air' ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}" data-i18n="air_express_tab">
                        Kargo Udara (Air)
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'delay', 'search' => request('search')]) }}#manifests-section" data-preserve-scroll 
                       class="px-4 py-2 rounded-xl font-bold transition whitespace-nowrap {{ ($filters['filter'] ?? '') === 'delay' ? 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}" data-i18n="exceptions_delay">
                        Kendala &amp; Gangguan
                    </a>
                </div>

                @if (auth()->check() && (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator())))
                <a href="{{ route('shipments.create') }}" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span data-i18n="new_manifest_btn">Manifest Baru</span>
                </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-3" data-i18n="tracking_id">Nomor Pelacakan</th>
                        <th class="py-3 px-3" data-i18n="mode">Moda</th>
                        <th class="py-3 px-3" data-i18n="consignee_carrier">Penerima &amp; Operator</th>
                        <th class="py-3 px-3" data-i18n="corridor_route">Rute Koridor</th>
                        <th class="py-3 px-3" data-i18n="status">Status</th>
                        <th class="py-3 px-3" data-i18n="schedule_impact">Dampak Jadwal</th>
                        <th class="py-3 px-3 text-right" data-i18n="action">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($manifests as $m)
                    <tr class="hover:bg-emerald-50/40 dark:hover:bg-slate-800/40 transition cursor-pointer group" onclick="focusOnShipment('{{ $m->shipment_number }}', {{ $m->origin->latitude }}, {{ $m->origin->longitude }}, {{ $m->destination->latitude }}, {{ $m->destination->longitude }})">
                        
                        <!-- Tracking ID -->
                        <td class="py-4 px-3 font-mono font-extrabold text-emerald-600 dark:text-emerald-400 group-hover:underline">
                            {{ $m->shipment_number }}
                        </td>

                        <!-- Mode Badge -->
                        <td class="py-4 px-3">
                            @if ($m->transport_mode === 'AIR')
                                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-mono text-[10px] font-bold">AIR</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-mono text-[10px] font-bold">OCEAN</span>
                            @endif
                        </td>

                        <!-- Consignee & Carrier -->
                        <td class="py-4 px-3">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $m->consignee_name ?? $m->customer->name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $m->carrier->name }}</div>
                        </td>

                        <!-- Corridor Route -->
                        <td class="py-4 px-3 font-mono">
                            <div class="text-slate-800 dark:text-slate-200 font-bold flex items-center gap-1.5">
                                <span>{{ $m->origin->code }}</span>
                                <span class="text-slate-400">&rarr;</span>
                                <span>{{ $m->destination->code }}</span>
                            </div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-sans truncate max-w-[200px]">
                                {{ $m->origin->name }} to {{ $m->destination->name }}
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-4 px-3 font-mono">
                            @php
                                $statusClasses = match($m->status) {
                                    'IN_TRANSIT' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                    'IN_FLIGHT' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                    'BERTHED' => 'bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800',
                                    'DELIVERED' => 'bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 border-emerald-300 dark:border-emerald-700',
                                    default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full border text-[10px] font-bold {{ $statusClasses }}">
                                {{ str_replace('_', ' ', $m->status) }}
                            </span>
                        </td>

                        <!-- Schedule Impact -->
                        <td class="py-4 px-3 font-mono text-xs">
                            @if ($m->delay_hours > 0)
                                <span class="text-amber-600 dark:text-amber-400 font-bold">+{{ $m->delay_hours }}h <span data-i18n="delay_suffix">Tertunda</span></span>
                            @else
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold" data-i18n="on_schedule">Tepat Waktu</span>
                            @endif
                        </td>

                        <!-- Action -->
                        <td class="py-4 px-3 text-right">
                            <a href="{{ route('shipments.show', $m) }}" 
                               class="inline-block px-3.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition shadow-2xs"
                               onclick="event.stopPropagation();" data-i18n="details_btn">
                                Rincian
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs" data-i18n="no_shipments_found">
                            Tidak ada pengiriman yang cocok dengan kriteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
            {{ $manifests->links() }}
        </div>
    </section>

    <!-- ==========================================
         GLOBAL INTEGRATED ALLIANCES CARRIER STRIP (Placed at bottom, right above footer)
         ========================================== -->
    <section class="border-t border-slate-200/80 dark:border-slate-800 pt-6 pb-2">
        @include('partials.carrier-alliances')
    </section>

</div>
@endsection

@push('scripts')
<script>
    // Leaflet Radar GIS Map Implementation with 100% Free OpenStreetMap Tiles
    let map;

    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Map centered on global maritime corridor
        map = L.map('radar-map', {
            zoomControl: true,
            attributionControl: true
        }).setView([20.0, 65.0], 3);

        // OpenStreetMap Tile Layer - 100% Free, NO API Key watermark
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        window.map = map;

        // 100% Water-Navigable Maritime Waypoint Route (Jakarta -> Singapore -> Suez -> Rotterdam)
        const completedSeaRoute = [
            [-6.1017, 106.8833], // Tanjung Priok (Jakarta Port)
            [-5.70, 106.85],     // Out to Java Sea
            [-3.00, 107.00],     // North Java Sea
            [-1.00, 106.00],     // East of Bangka
            [0.80, 104.50],      // South Singapore Strait Approach
            [1.25, 104.05],      // Singapore Strait East
            [1.2644, 103.82],    // Port of Singapore (PSA)
            [1.22, 103.55],      // Singapore Strait West
            [2.10, 102.00],      // Malacca Strait (off Malacca)
            [3.00, 101.00],      // Malacca Strait (off Port Klang)
            [5.00, 98.50],       // Malacca Strait (off Penang / North Sumatra)
            [6.00, 95.00],       // Andaman Sea Exit (North of Aceh)
            [5.70, 80.50],       // Indian Ocean (South of Sri Lanka / Dondra Head)
            [9.50, 65.00],       // Central Arabian Sea
            [12.00, 52.00],      // Arabian Sea (North of Socotra)
            [12.20, 46.50],      // Central Gulf of Aden
            [12.60, 43.40],      // Bab-el-Mandeb Strait (Navigable Chokepoint)
            [14.50, 42.00],      // Southern Red Sea Channel
            [19.00, 39.00],      // Central Red Sea
            [25.50, 35.50],      // Northern Red Sea
            [27.75, 33.70],      // Entrance to Gulf of Suez
            [28.90, 33.00],      // Gulf of Suez Channel
            [29.93, 32.56]       // Suez Canal Southern Terminal (Current Lead Vessel Location!)
        ];

        const remainingSeaRoute = [
            [29.93, 32.56],      // Suez Canal South (Current Position)
            [30.40, 32.35],      // Great Bitter Lake
            [31.28, 32.31],      // Port Said (Mediterranean Entrance)
            [34.30, 25.00],      // Mediterranean Sea (South of Crete)
            [36.00, 18.00],      // Ionian Sea
            [37.20, 11.50],      // Strait of Sicily (Between Sicily and Tunisia)
            [38.00, 8.00],       // South of Sardinia
            [38.50, 2.50],       // Balearic Sea
            [36.20, -3.00],      // Alboran Sea
            [35.95, -5.60],      // Strait of Gibraltar
            [36.00, -6.50],      // Atlantic Ocean Exit
            [36.90, -9.20],      // Rounding Cape St. Vincent (Portugal)
            [38.70, -9.60],      // Atlantic off Lisbon
            [43.00, -9.60],      // Rounding Cape Finisterre (Spain)
            [46.50, -6.00],      // Crossing Bay of Biscay (Open Atlantic)
            [49.50, -4.50],      // English Channel Western Approaches
            [50.20, -1.00],      // English Channel Mid
            [51.10, 1.60],       // Strait of Dover
            [51.90, 3.80],       // North Sea Southern Approach
            [51.9547, 4.0531]    // Port of Rotterdam (Maasvlakte Europort)
        ];

        // Air Express Route (Singapore -> Frankfurt)
        const airRoute = [
            [1.3644, 103.9915],  // Singapore Changi
            [12.0, 88.0],        // Bay of Bengal
            [19.0, 73.0],        // Indian Airspace
            [25.0, 58.0],        // Gulf of Oman
            [33.0, 44.0],        // Middle East Corridor
            [43.0, 25.0],        // Southeast Europe
            [50.0379, 8.5622]    // Frankfurt CargoCity
        ];

        // Custom Marker Icons (Vector SVG pins in Emerald Green)
        const vesselIcon = L.divIcon({
            className: 'custom-vessel-marker',
            html: `<div class="w-9 h-9 rounded-full bg-emerald-600 border-2 border-white shadow-xl flex items-center justify-center text-white transform -translate-x-1/2 -translate-y-1/2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1 16l3.5 5h14.5l3-5H1z"/>
                    <rect x="4" y="11" width="3" height="4" rx="0.5"/>
                    <rect x="8" y="11" width="3" height="4" rx="0.5"/>
                    <rect x="12" y="11" width="3" height="4" rx="0.5"/>
                    <rect x="6" y="7.5" width="3" height="3" rx="0.5"/>
                    <rect x="10" y="7.5" width="3" height="3" rx="0.5"/>
                    <path d="M16.5 9h3.5v6h-3.5V9zm.5-3h1.5v2H17V6z"/>
                </svg>
            </div>`,
            iconSize: [0, 0]
        });

        const planeIcon = L.divIcon({
            className: 'custom-plane-marker',
            html: `<div class="w-9 h-9 rounded-full bg-emerald-500 border-2 border-white shadow-xl flex items-center justify-center text-white transform -translate-x-1/2 -translate-y-1/2"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg></div>`,
            iconSize: [0, 0]
        });

        const hubIcon = L.divIcon({
            className: 'custom-hub-marker',
            html: `<div class="w-3.5 h-3.5 rounded-full bg-slate-900 border-2 border-white shadow transform -translate-x-1/2 -translate-y-1/2"></div>`,
            iconSize: [0, 0]
        });

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
            // ADMIN & OPERATOR: Full Access to all fleet corridors & live assets
            L.polyline(completedSeaRoute, {
                color: '#059669',
                weight: 4,
                opacity: 0.95,
                smoothFactor: 1
            }).addTo(map);

            L.polyline(remainingSeaRoute, {
                color: '#F59E0B',
                dashArray: '6, 8',
                weight: 3,
                opacity: 0.9
            }).addTo(map);

            L.polyline(airRoute, {
                color: '#10B981',
                dashArray: '4, 6',
                weight: 3,
                opacity: 0.95
            }).addTo(map);

            // Hub Markers
            L.marker([-6.1017, 106.8833], {icon: hubIcon}).addTo(map).bindPopup("<b>Tanjung Priok (IDTPP)</b><br>Origin Port");
            L.marker([1.2644, 103.82], {icon: hubIcon}).addTo(map).bindPopup("<b>Port of Singapore (SGSIN)</b><br>Transshipment Hub");
            L.marker([51.9547, 4.0531], {icon: hubIcon}).addTo(map).bindPopup("<b>Port of Rotterdam (NLRTM)</b><br>Destination Terminal");
            L.marker([50.0379, 8.5622], {icon: hubIcon}).addTo(map).bindPopup("<b>Frankfurt CargoCity (DEFRA)</b><br>Air Gateway");

            // Active Vessel at Suez Canal Anchorage (100% water!)
            L.marker([29.93, 32.56], {icon: vesselIcon}).addTo(map)
                .bindPopup("<div class='font-sans text-xs'><b>AT-2026-B8910 (Maersk Line)</b><br>Suez Canal Convoy Anchorage<br><span class='text-amber-600 font-bold'>Delay: +28h</span></div>")
                .openPopup();

            // Active Plane in Flight
            L.marker([19.0, 73.0], {icon: planeIcon}).addTo(map)
                .bindPopup("<div class='font-sans text-xs'><b>AWB-772-91024 (Qatar Cargo)</b><br>Altitude: 36,000 ft | Speed: 485 Kts<br><span class='text-emerald-600 font-bold'>On Schedule</span></div>");
        @else
            // CUSTOMER ROLE: Only plot route for searched/selected consignment
            @if($leadShipment)
                const oLat = {{ $leadShipment->origin->latitude }};
                const oLng = {{ $leadShipment->origin->longitude }};
                const dLat = {{ $leadShipment->destination->latitude }};
                const dLng = {{ $leadShipment->destination->longitude }};

                L.marker([oLat, oLng], {icon: hubIcon}).addTo(map).bindPopup("<b>Asal: {{ $leadShipment->origin->name }} ({{ $leadShipment->origin->code }})</b>");
                L.marker([dLat, dLng], {icon: hubIcon}).addTo(map).bindPopup("<b>Tujuan: {{ $leadShipment->destination->name }} ({{ $leadShipment->destination->code }})</b>");

                @if($leadShipment->transport_mode === 'AIR')
                    const custRoute = [
                        [oLat, oLng],
                        [12.0, 88.0],
                        [19.0, 73.0],
                        [33.0, 44.0],
                        [dLat, dLng]
                    ];
                    L.polyline(custRoute, {
                        color: '#10B981',
                        dashArray: '4, 6',
                        weight: 4,
                        opacity: 0.95
                    }).addTo(map);

                    L.marker([19.0, 73.0], {icon: planeIcon}).addTo(map)
                        .bindPopup("<div class='font-sans text-xs'><b>{{ $leadShipment->shipment_number }}</b><br>Moda: Kargo Udara<br>Kecepatan: 485 Kts</div>")
                        .openPopup();

                    map.fitBounds(L.latLngBounds(custRoute), {padding: [50, 50]});
                @else
                    L.polyline(completedSeaRoute, {
                        color: '#059669',
                        weight: 4,
                        opacity: 0.95
                    }).addTo(map);

                    L.polyline(remainingSeaRoute, {
                        color: '#F59E0B',
                        dashArray: '6, 8',
                        weight: 3,
                        opacity: 0.9
                    }).addTo(map);

                    L.marker([29.93, 32.56], {icon: vesselIcon}).addTo(map)
                        .bindPopup("<div class='font-sans text-xs'><b>{{ $leadShipment->shipment_number }}</b><br>Kapal: {{ $leadShipment->carrier->name }}<br>Status: Dalam Pelayaran (Suez)</div>")
                        .openPopup();

                    map.fitBounds([[-6.1, 106.8], [52.0, 4.0]], {padding: [40, 40]});
                @endif
            @else
                map.setView([15.0, 80.0], 3);
            @endif
        @endif

        // Ensure Leaflet resizes correctly within responsive cards
        setTimeout(() => {
            map.invalidateSize();
        }, 300);
    });

    // Countdown Timer Simulation
    let totalSeconds = 15 * 3600 + 42 * 60 + 19;
    setInterval(() => {
        if (totalSeconds > 0) totalSeconds--;
        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;
        const el = document.getElementById('berthing-countdown');
        if (el) el.innerText = `${h}h ${String(m).padStart(2, '0')}m ${String(s).padStart(2, '0')}s`;
    }, 1000);
</script>
@endpush
