@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- 1. Next Port Berthing Alert / Countdown Banner -->
    <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/60 font-mono text-[11px] font-bold rounded-xl tracking-wider uppercase whitespace-nowrap shadow-sm" data-i18n="next_port">
                NEXT PORT BERTHING
            </span>
            <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">
                @if ($metrics['next_berthing'])
                    <span class="text-slate-900 dark:text-white font-extrabold">{{ $metrics['next_berthing']->destination->name }} ({{ $metrics['next_berthing']->destination->code }})</span>
                    • <span class="text-slate-500">{{ $metrics['next_berthing']->berth_number ?? 'Berth 402' }}</span>
                    <span class="text-slate-400 font-normal text-xs hidden sm:inline">(Vessel {{ $metrics['next_berthing']->vessel_or_flight }})</span>
                @else
                    <span class="text-slate-900 dark:text-white font-extrabold">Port of Rotterdam (NLRTM)</span> • Berth 402 (Vessel Maersk Mc-Kinney Moller)
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 sm:gap-4 font-mono text-xs">
            <div class="flex items-center gap-2 bg-slate-900 dark:bg-[#070A10] text-white px-3.5 py-1.5 rounded-xl shadow-sm">
                <span class="text-slate-400 text-[10px] uppercase font-bold" data-i18n="eta_countdown">ETA COUNTDOWN</span>
                <span id="berthing-countdown" class="text-cyan-400 font-bold tracking-wider">15h 42m 19s</span>
            </div>
            <div class="hidden sm:flex items-center gap-1.5 text-slate-500 dark:text-slate-400 text-[11px]">
                <span class="uppercase">SCHEDULED:</span>
                <span class="text-slate-800 dark:text-slate-200 font-bold">
                    {{ $metrics['next_berthing'] ? $metrics['next_berthing']->expected_arrival->format('d M Y') : '04 NOV 2026' }}
                </span>
            </div>
        </div>
    </div>

    <!-- 2. Pastel KPI Cards (Matching "Your Assets" in Reference Image 2, No Box Emojis) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Total Consignments (Soft Blue) -->
        <div class="bg-[#F0F5FA] dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span data-i18n="total_consignments">Total Consignments</span>
                <span class="w-7 h-7 rounded-xl bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="my-2">
                <div class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ $metrics['total_consignments'] }} B/L &amp; AWB
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Active global corridors</div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-800 text-[11px]">
                <span class="text-slate-400 font-mono">{{ $metrics['in_transit_count'] }} in transit</span>
                <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-bold font-mono text-[10px]">+100% active</span>
            </div>
        </div>

        <!-- Card 2: Air Express Fleet (Soft Lavender/Purple) -->
        <div class="bg-[#F3EEFB] dark:bg-purple-950/30 border border-purple-200/60 dark:border-purple-800/40 rounded-3xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span data-i18n="air_express">Air Express Lanes</span>
                <span class="w-7 h-7 rounded-xl bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </span>
            </div>
            <div class="my-2">
                <div class="text-2xl lg:text-3xl font-extrabold text-purple-900 dark:text-purple-200 tracking-tight">
                    3 Flights
                </div>
                <div class="text-[11px] text-purple-700/80 dark:text-purple-300 font-medium">High priority express</div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-purple-200/60 dark:border-purple-800/40 text-[11px]">
                <span class="text-slate-500 dark:text-slate-400 font-mono">Speed 485 Kts</span>
                <span class="px-2 py-0.5 rounded-full bg-purple-200 dark:bg-purple-900 text-purple-800 dark:text-purple-200 font-bold font-mono text-[10px]">+0.14%</span>
            </div>
        </div>

        <!-- Card 3: Ocean Lines Fleet (Soft Mint/Emerald) -->
        <div class="bg-[#EBF7F0] dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40 rounded-3xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span data-i18n="ocean_lines">Ocean Lines Fleet</span>
                <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </span>
            </div>
            <div class="my-2">
                <div class="text-2xl lg:text-3xl font-extrabold text-emerald-900 dark:text-emerald-200 tracking-tight">
                    9 Vessels
                </div>
                <div class="text-[11px] text-emerald-700/80 dark:text-emerald-300 font-medium">{{ $metrics['carrier_count'] }} Partner carriers</div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-emerald-200/60 dark:border-emerald-800/40 text-[11px]">
                <span class="text-slate-500 dark:text-slate-400 font-mono">Maersk, EVG, MSC</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-200 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 font-bold font-mono text-[10px]">+0.31%</span>
            </div>
        </div>

        <!-- Card 4: On-Time Reliability (Soft Cream/Amber) -->
        <div class="bg-[#FCF7E8] dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40 rounded-3xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span data-i18n="ontime_reliability">On-Time Reliability</span>
                <span class="w-7 h-7 rounded-xl bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="my-2">
                <div class="text-2xl lg:text-3xl font-extrabold text-amber-900 dark:text-amber-200 tracking-tight">
                    {{ $metrics['on_time_reliability'] }}%
                </div>
                <div class="text-[11px] text-amber-700/80 dark:text-amber-300 font-medium">Multimodal network rate</div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-amber-200/60 dark:border-amber-800/40 text-[11px]">
                <span class="text-slate-500 dark:text-slate-400 font-mono">Benchmark: 90%</span>
                <span class="px-2 py-0.5 rounded-full bg-amber-200 dark:bg-amber-900 text-amber-800 dark:text-amber-200 font-bold font-mono text-[10px]">+0.27%</span>
            </div>
        </div>

    </div>

    <!-- 3. Upper Grid: GIS OpenStreetMap Radar (Left) + Sensor & Incident Desk (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: OpenStreetMap Interactive Live Map Container -->
        <div class="lg:col-span-8 bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            
            <!-- Map Header -->
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 radar-pulse-dot"></span>
                    <span class="text-xs font-mono font-extrabold text-slate-900 dark:text-white uppercase tracking-wider" data-i18n="live_radar">
                        LIVE GIS AIS &amp; ADS-B RADAR
                    </span>
                    <span class="text-[11px] text-slate-400 font-mono hidden sm:inline">
                        FOCUS: <strong class="text-slate-700 dark:text-slate-200">{{ $leadShipment->shipment_number ?? 'AT-2026-B8910' }}</strong>
                    </span>
                </div>

                <div class="flex items-center gap-2 font-mono text-[11px]">
                    <span class="px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/50 font-bold">
                        AIR: {{ $leadShipment->air_speed_kts ?? 485 }} KTS
                    </span>
                    <span class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700/50 font-bold">
                        SEA DELAY: +{{ $leadShipment->delay_hours ?? 28 }}H
                    </span>
                </div>
            </div>

            <!-- Free, 100% Valid OpenStreetMap Tile Viewport (No API Key Required!) -->
            <div class="relative w-full h-[380px] sm:h-[420px] rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-inner bg-slate-100 dark:bg-[#070A10]">
                <div id="radar-map" class="w-full h-full z-10"></div>

                <!-- Map Modes Legend Overlay -->
                <div class="absolute bottom-3 right-3 bg-white/95 dark:bg-[#0F172A]/90 backdrop-blur-md border border-slate-200 dark:border-[#233550] rounded-xl p-3 text-[10px] font-mono space-y-1.5 shadow-xl z-[1000]">
                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1" data-i18n="modes_legend">Modes Legend</div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-1 bg-cyan-500 rounded"></span>
                        <span class="text-slate-700 dark:text-slate-200 font-medium">Ocean Vessel (Jakarta &rarr; Suez)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-0.5 border-t-2 border-dashed border-amber-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Remaining Ocean (Suez &rarr; RTM)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-1 bg-emerald-500 rounded"></span>
                        <span class="text-slate-700 dark:text-slate-200 font-medium">Air Freight (SIN &rarr; FRA)</span>
                    </div>
                </div>
            </div>

            <!-- Telemetry Row below Map -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 mt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-mono">Tracked Cargo Manifest</span>
                    <span class="text-slate-800 dark:text-slate-200 font-bold">Ocean Container &amp; Air Express</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-mono">Combined Payload</span>
                    <span class="text-slate-900 dark:text-white font-extrabold font-mono">31,900.50 KG <span class="text-slate-400 text-[10px]">(Sea + Air)</span></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-mono">Air Delivery ETA</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold font-mono">Today • 21:30 UTC</span>
                </div>
            </div>

            <!-- Voyage Milestones Ocean Lead Progress -->
            <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="font-bold uppercase tracking-wider text-slate-400 text-[10px] font-mono">Voyage Milestones (Ocean Lead)</span>
                    <span class="font-mono text-cyan-600 dark:text-cyan-400 text-[11px] font-bold">
                        Stage {{ $leadShipment->current_stage ?? 3 }} of {{ $leadShipment->total_stages ?? 4 }} Active
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                    <!-- Stage 1 -->
                    <div class="bg-slate-50 dark:bg-[#162235] border border-slate-200 dark:border-[#233550] rounded-xl p-2.5">
                        <span class="text-[9px] text-slate-400 uppercase font-mono block">Stage 1</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white block">{{ $leadShipment->origin->name ?? 'Tanjung Priok' }}</span>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-semibold">&#10003; Departed</span>
                    </div>

                    <!-- Stage 2 -->
                    <div class="bg-slate-50 dark:bg-[#162235] border border-slate-200 dark:border-[#233550] rounded-xl p-2.5">
                        <span class="text-[9px] text-slate-400 uppercase font-mono block">Stage 2</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white block">Singapore Hub</span>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-semibold">&#10003; Cleared</span>
                    </div>

                    <!-- Stage 3 (Active) -->
                    <div class="bg-cyan-50 dark:bg-cyan-950/60 border-2 border-cyan-500 rounded-xl p-2.5 shadow-sm">
                        <span class="text-[9px] text-cyan-700 dark:text-cyan-300 uppercase font-mono font-bold block">Stage 3</span>
                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Suez Canal</span>
                        <span class="text-[10px] text-amber-600 dark:text-amber-400 font-mono font-bold">Anchored (Wait)</span>
                    </div>

                    <!-- Stage 4 -->
                    <div class="bg-slate-50 dark:bg-[#162235] border border-slate-200 dark:border-[#233550] rounded-xl p-2.5 opacity-60">
                        <span class="text-[9px] text-slate-400 uppercase font-mono block">Stage 4</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white block">{{ $leadShipment->destination->name ?? 'Rotterdam Hub' }}</span>
                        <span class="text-[10px] text-slate-400 font-mono">Scheduled</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Telemetry & Incident Notice Column (Dark Card & Sensor Widgets) -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            
            <!-- Dark Banner Card (matching "Earn free crypto" banner in Reference Image 2) -->
            <div class="bg-[#10141E] dark:bg-[#070A0F] text-white rounded-3xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-10 -bottom-10 w-36 h-36 rounded-full bg-emerald-500/20 blur-2xl pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="flex items-center gap-2 text-xs font-mono font-bold text-amber-400 uppercase" data-i18n="incident_notice">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            Active Incident Notice
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-red-500/20 text-red-400 border border-red-500/40 text-[10px] font-mono font-bold uppercase">
                            High
                        </span>
                    </div>

                    <h3 class="text-lg font-extrabold text-white tracking-tight mt-1">Suez Canal Convoy Delay</h3>
                    <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                        Queue congestion at Suez entrance. Vessel delayed by <strong class="text-amber-400">+28 hours</strong>. Air flight QR-803F operating normally.
                    </p>
                </div>

                <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-800 text-xs font-mono">
                    <span class="text-slate-400">Detected 9h ago</span>
                    @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                    <a href="{{ route('exceptions.index') }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition text-[11px] flex items-center gap-1">
                        <span>View Desk</span> &rarr;
                    </a>
                    @else
                    <span class="text-amber-400 font-bold">Investigating</span>
                    @endif
                </div>
            </div>

            <!-- Reefer Container Sensors Card -->
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300 uppercase flex items-center gap-2" data-i18n="reefer_sensors">
                        <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Reefer Container Sensors
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/50 text-[10px] font-mono font-bold">
                        Normal
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 font-mono">
                    <div class="bg-slate-50 dark:bg-[#070A10] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3.5">
                        <span class="text-[10px] text-slate-400 uppercase block mb-1">Temperature</span>
                        <div class="text-2xl font-black text-slate-900 dark:text-white">4.2°C</div>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">±0.2°C Stable</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-[#070A10] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3.5">
                        <span class="text-[10px] text-slate-400 uppercase block mb-1">Humidity</span>
                        <div class="text-2xl font-black text-slate-900 dark:text-white">62%</div>
                        <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-semibold">Optimal</span>
                    </div>
                </div>
            </div>

            <!-- Flight Telemetry (ADS-B) Card -->
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300 uppercase flex items-center gap-1.5" data-i18n="flight_telemetry">
                        <span class="text-emerald-500 font-black">+</span>
                        Flight Telemetry (ADS-B)
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-cyan-600 dark:text-cyan-400 border border-slate-200 dark:border-slate-700 text-[10px] font-mono">
                        QR-772-91024
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 font-mono">
                    <div class="bg-slate-50 dark:bg-[#070A10] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3.5">
                        <span class="text-[10px] text-slate-400 uppercase block mb-1">Flight / Craft</span>
                        <div class="text-sm font-bold text-slate-900 dark:text-white">QR-803F</div>
                        <span class="text-[10px] text-slate-400">Boeing 777-200F</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-[#070A10] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3.5">
                        <span class="text-[10px] text-slate-400 uppercase block mb-1">Air Speed &amp; Alt</span>
                        <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400">485 Kts</div>
                        <span class="text-[10px] text-slate-400">FL360 (36,000 ft)</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- 4. Active Consignment Manifests Table (Clean Modern Table matching Image 2) -->
    <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        
        <!-- Table Top Bar: Title & Filter Pills -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white tracking-tight" data-i18n="active_manifests_title">
                    Active Consignment Manifests
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400" data-i18n="active_manifests_sub">
                    Click any consignment row to zoom camera and track on the live map
                </p>
            </div>

            <!-- Filter Pills & Search -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-[#070A10] p-1 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-x-auto text-xs">
                    <a href="{{ route('dashboard', ['filter' => 'all', 'search' => request('search')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ ($filters['filter'] ?? 'all') === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}" data-i18n="all_consignments">
                        All Consignments
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'ocean', 'search' => request('search')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ ($filters['filter'] ?? '') === 'ocean' ? 'bg-cyan-50 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}" data-i18n="ocean_lines">
                        Ocean Lines
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'air', 'search' => request('search')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ ($filters['filter'] ?? '') === 'air' ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}" data-i18n="air_express">
                        Air Express
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'delay', 'search' => request('search')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ ($filters['filter'] ?? '') === 'delay' ? 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}" data-i18n="exceptions_delay">
                        Exceptions (Delay)
                    </a>
                </div>

                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                <a href="{{ route('shipments.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>New Manifest</span>
                </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-3" data-i18n="tracking_id">Tracking Identifier</th>
                        <th class="py-3 px-3" data-i18n="mode">Mode</th>
                        <th class="py-3 px-3" data-i18n="consignee_carrier">Consignee &amp; Carrier</th>
                        <th class="py-3 px-3" data-i18n="corridor_route">Corridor Route</th>
                        <th class="py-3 px-3" data-i18n="status">Status</th>
                        <th class="py-3 px-3" data-i18n="schedule_impact">Schedule Impact</th>
                        <th class="py-3 px-3 text-right" data-i18n="action">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($manifests as $m)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition cursor-pointer group" onclick="focusOnShipment('{{ $m->shipment_number }}', {{ $m->origin->latitude }}, {{ $m->origin->longitude }}, {{ $m->destination->latitude }}, {{ $m->destination->longitude }})">
                        
                        <!-- Tracking ID -->
                        <td class="py-4 px-3 font-mono font-extrabold text-emerald-600 dark:text-cyan-400 group-hover:underline">
                            {{ $m->shipment_number }}
                        </td>

                        <!-- Mode Badge -->
                        <td class="py-4 px-3">
                            @if ($m->transport_mode === 'AIR')
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/50 font-mono text-[10px] font-bold">AIR</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg bg-cyan-50 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700/50 font-mono text-[10px] font-bold">OCEAN</span>
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
                                    'IN_TRANSIT' => 'bg-cyan-50 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-700/50',
                                    'IN_FLIGHT' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700/50',
                                    'BERTHED' => 'bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-700/50',
                                    'DELIVERED' => 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-700/50',
                                    default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg border text-[10px] font-bold {{ $statusClasses }}">
                                {{ str_replace('_', ' ', $m->status) }}
                            </span>
                        </td>

                        <!-- Schedule Impact -->
                        <td class="py-4 px-3 font-mono text-xs">
                            @if ($m->delay_hours > 0)
                                <span class="text-amber-600 dark:text-amber-400 font-bold">+{{ $m->delay_hours }}h Delay</span>
                            @else
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">On Schedule</span>
                            @endif
                        </td>

                        <!-- Action -->
                        <td class="py-4 px-3 text-right">
                            <a href="{{ route('shipments.show', $m) }}" 
                               class="inline-block px-3.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-600 hover:text-white text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition shadow-sm"
                               onclick="event.stopPropagation();" data-i18n="details_btn">
                                Details
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                            No shipments found matching criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
            {{ $manifests->links() }}
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Leaflet Radar GIS Map Implementation with 100% Free OpenStreetMap Tiles (No API Key Required!)
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

        // Sea Corridor Route (Jakarta -> Singapore -> Suez -> Rotterdam)
        const seaRoute = [
            [-6.1017, 106.8833], // Tanjung Priok
            [1.2902, 103.8519],  // Singapore
            [6.0, 80.0],         // Sri Lanka South Cape
            [12.5, 50.0],        // Gulf of Aden
            [29.9737, 32.5599],  // Suez Canal
            [36.0, 14.0],        // Mediterranean
            [36.0, -5.5],        // Gibraltar
            [51.9547, 4.0531]    // Rotterdam
        ];

        // Completed corridor line (Cyan)
        L.polyline(seaRoute.slice(0, 5), {
            color: '#06B6D4',
            weight: 4,
            opacity: 0.95,
            smoothFactor: 1
        }).addTo(map);

        // Remaining corridor line (Amber dashed)
        L.polyline(seaRoute.slice(4), {
            color: '#F59E0B',
            dashArray: '6, 8',
            weight: 3,
            opacity: 0.9
        }).addTo(map);

        // Air Express Route (Singapore -> Frankfurt)
        const airRoute = [
            [1.3644, 103.9915],  // Singapore
            [18.5204, 73.8567],  // Arabian Sea waypoint
            [50.0379, 8.5622]    // Frankfurt
        ];

        L.polyline(airRoute, {
            color: '#10B981',
            dashArray: '4, 6',
            weight: 3,
            opacity: 0.95
        }).addTo(map);

        // Custom Marker Icons (Vector SVG pins)
        const vesselIcon = L.divIcon({
            className: 'custom-vessel-marker',
            html: `<div class="w-8 h-8 rounded-full bg-cyan-600 border-2 border-white shadow-lg flex items-center justify-center text-white transform -translate-x-1/2 -translate-y-1/2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>`,
            iconSize: [0, 0]
        });

        const planeIcon = L.divIcon({
            className: 'custom-plane-marker',
            html: `<div class="w-8 h-8 rounded-full bg-emerald-600 border-2 border-white shadow-lg flex items-center justify-center text-white transform -translate-x-1/2 -translate-y-1/2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></div>`,
            iconSize: [0, 0]
        });

        const hubIcon = L.divIcon({
            className: 'custom-hub-marker',
            html: `<div class="w-3.5 h-3.5 rounded-full bg-slate-900 border-2 border-white shadow transform -translate-x-1/2 -translate-y-1/2"></div>`,
            iconSize: [0, 0]
        });

        // Hub Markers
        L.marker([-6.1017, 106.8833], {icon: hubIcon}).addTo(map).bindPopup("<b>Tanjung Priok (IDTPP)</b><br>Origin Port");
        L.marker([1.2902, 103.8519], {icon: hubIcon}).addTo(map).bindPopup("<b>Port of Singapore (SGSIN)</b><br>Transshipment Hub");
        L.marker([51.9547, 4.0531], {icon: hubIcon}).addTo(map).bindPopup("<b>Port of Rotterdam (NLRTM)</b><br>Destination Terminal");
        L.marker([50.0379, 8.5622], {icon: hubIcon}).addTo(map).bindPopup("<b>Frankfurt CargoCity (DEFRA)</b><br>Air Gateway");

        // Active Vessel at Suez
        L.marker([29.9737, 32.5599], {icon: vesselIcon}).addTo(map)
            .bindPopup("<div class='font-sans text-xs'><b>AT-2026-B8910 (Maersk Line)</b><br>Suez Canal Convoy Anchorage<br><span class='text-red-600 font-bold'>Delay: +28h</span></div>")
            .openPopup();

        // Active Plane in Flight
        L.marker([18.5204, 73.8567], {icon: planeIcon}).addTo(map)
            .bindPopup("<div class='font-sans text-xs'><b>AWB-772-91024 (Qatar Cargo)</b><br>Altitude: 36,000 ft | Speed: 485 Kts<br><span class='text-emerald-600 font-bold'>On Schedule</span></div>");

        // Ensure Leaflet resizes correctly within responsive cards
        setTimeout(() => {
            map.invalidateSize();
        }, 300);
    });

    function focusOnShipment(num, lat1, lng1, lat2, lng2) {
        if (!map) return;
        const midLat = (lat1 + lat2) / 2;
        const midLng = (lng1 + lng2) / 2;
        map.flyTo([midLat, midLng], 4, {duration: 1.5});
    }

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
