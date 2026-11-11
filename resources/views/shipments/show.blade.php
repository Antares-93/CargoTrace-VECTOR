@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Breadcrumb & Status Pill -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('shipments.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 transition">
                    &larr; Consignments
                </a>
                <span class="text-slate-600">/</span>
                <h1 class="text-xl sm:text-2xl font-black font-mono text-cyan-400 tracking-tight">
                    {{ $shipment->shipment_number }}
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase {{ $shipment->transport_mode === 'AIR' ? 'bg-emerald-950 text-emerald-300 border border-emerald-700/60' : 'bg-cyan-950 text-cyan-300 border border-cyan-700/60' }}">
                    {{ $shipment->transport_mode }}
                </span>
            </div>
            <div class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                <span>Consignee: <strong class="text-white">{{ $shipment->consignee_name ?? $shipment->customer->name }}</strong></span>
                <span>•</span>
                <span>Carrier: <strong class="text-slate-200">{{ $shipment->carrier->name }}</strong></span>
                <span>•</span>
                <span>Craft/Vessel: <strong class="text-slate-200 font-mono">{{ $shipment->vessel_or_flight ?? 'N/A' }}</strong></span>
            </div>
        </div>

        <!-- Quick Action Buttons for Operators & Admins -->
        <div class="flex flex-wrap items-center gap-2">
            @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                @if ($shipment->status !== \App\Models\Shipment::STATUS_DELIVERED)
                <button onclick="document.getElementById('modal-tracking').classList.remove('hidden')" 
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Tracking Event</span>
                </button>
                <button onclick="document.getElementById('modal-exception').classList.remove('hidden')" 
                        class="px-3.5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Report Exception</span>
                </button>
                <a href="{{ route('shipments.edit', $shipment) }}" class="px-3 py-2 bg-[#1A273D] hover:bg-[#233550] text-slate-300 text-xs font-semibold rounded-xl border border-[#233550] transition">
                    Edit Details
                </a>
                @else
                <span class="px-3 py-1.5 rounded-xl bg-purple-950 text-purple-300 border border-purple-700/60 font-mono text-xs font-bold">
                    Terminal State: Delivered
                </span>
                @endif
            @endif
        </div>
    </div>

    <!-- Corridor & Milestone Progression Bar -->
    <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-mono font-bold uppercase text-slate-400 tracking-wider">
                Corridor Route Milestones
            </span>
            <div class="flex items-center gap-2 font-mono text-xs">
                <span class="text-slate-400">Status:</span>
                <span class="px-2 py-0.5 rounded bg-cyan-950 text-cyan-300 border border-cyan-700/50 font-bold uppercase">
                    {{ str_replace('_', ' ', $shipment->status) }}
                </span>
                @if ($shipment->delay_hours > 0)
                    <span class="px-2 py-0.5 rounded bg-amber-950 text-amber-300 border border-amber-700/50 font-bold">
                        +{{ $shipment->delay_hours }}h Impact ({{ $shipment->delay_status }})
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded bg-emerald-950 text-emerald-300 border border-emerald-700/50 font-bold">
                        On Schedule
                    </span>
                @endif
            </div>
        </div>

        <!-- Milestones Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
            
            <div class="bg-[#162235] border border-[#233550] rounded-xl p-3 {{ $shipment->current_stage >= 1 ? 'border-emerald-500/50' : '' }}">
                <span class="text-[10px] text-slate-400 uppercase font-mono block">Origin Node</span>
                <div class="text-sm font-bold text-white mt-0.5">{{ $shipment->origin->name }}</div>
                <span class="text-xs text-cyan-400 font-mono">({{ $shipment->origin->code }})</span>
                <div class="text-[10px] text-slate-400 mt-1 font-mono">
                    Dep: {{ $shipment->actual_departure ? $shipment->actual_departure->format('d M H:i') : $shipment->expected_departure->format('d M H:i') }}
                </div>
            </div>

            <div class="bg-[#162235] border border-[#233550] rounded-xl p-3 {{ $shipment->current_stage >= 2 ? 'border-emerald-500/50' : 'opacity-60' }}">
                <span class="text-[10px] text-slate-400 uppercase font-mono block">Stage 2: Mid-Corridor Transit</span>
                <div class="text-sm font-bold text-white mt-0.5">International Waters / Airway</div>
                <span class="text-xs text-slate-400 font-mono">Waypoint Check</span>
                <div class="text-[10px] text-emerald-400 mt-1 font-mono font-semibold">
                    {{ $shipment->current_stage >= 2 ? '✓ Cleared' : 'En Route' }}
                </div>
            </div>

            <div class="bg-[#162235] border border-[#233550] rounded-xl p-3 {{ $shipment->current_stage == 3 ? 'border-cyan-400 bg-cyan-950/40 shadow-[0_0_15px_rgba(6,182,212,0.15)]' : ($shipment->current_stage > 3 ? 'border-emerald-500/50' : 'opacity-60') }}">
                <span class="text-[10px] uppercase font-mono block {{ $shipment->current_stage == 3 ? 'text-cyan-300 font-bold' : 'text-slate-400' }}">Stage 3: Approach Hub</span>
                <div class="text-sm font-bold text-white mt-0.5">{{ $shipment->destination->type === 'SEAPORT' ? 'Anchorage / Canal Entry' : 'Approach Control' }}</div>
                <span class="text-xs text-slate-400 font-mono">Telemetry Active</span>
                <div class="text-[10px] {{ $shipment->delay_hours > 0 ? 'text-amber-400 font-bold' : 'text-emerald-400' }} mt-1 font-mono">
                    {{ $shipment->delay_hours > 0 ? 'Wait / Regulated' : 'Approaching' }}
                </div>
            </div>

            <div class="bg-[#162235] border border-[#233550] rounded-xl p-3 {{ $shipment->status === \App\Models\Shipment::STATUS_DELIVERED ? 'border-purple-500/60 bg-purple-950/30' : 'opacity-60' }}">
                <span class="text-[10px] text-slate-400 uppercase font-mono block">Destination Terminal</span>
                <div class="text-sm font-bold text-white mt-0.5">{{ $shipment->destination->name }}</div>
                <span class="text-xs text-cyan-400 font-mono">({{ $shipment->destination->code }})</span>
                <div class="text-[10px] text-slate-400 mt-1 font-mono">
                    ETA: {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('d M H:i') : $shipment->expected_arrival->format('d M H:i') }}
                </div>
            </div>

        </div>
    </div>

    <!-- Main Grid: Map & Weather Telemetry (Left) + Cargo & Sensor Manifest (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left 7 Cols: Route GIS Map + Live Open-Meteo Weather Cards -->
        <div class="lg:col-span-7 space-y-5">
            
            <!-- Map Container -->
            <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-4 shadow-xl">
                <div class="flex items-center justify-between mb-3 text-xs font-mono">
                    <span class="font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                        LIVE GIS CORRIDOR RADAR
                    </span>
                    <span class="text-slate-400">
                        {{ $shipment->origin->code }} &rarr; {{ $shipment->destination->code }}
                    </span>
                </div>

                <div id="shipment-map" class="w-full h-80 sm:h-96 rounded-xl border border-[#1E293B] bg-[#0B0F17]"></div>
            </div>

            <!-- External Weather API Integration (Slide 9 Requirement) -->
            <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono font-bold text-slate-300 uppercase flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        External Weather Telemetry (Open-Meteo Integration)
                    </span>
                    <span class="text-[10px] font-mono text-emerald-400 bg-emerald-950 px-2 py-0.5 rounded border border-emerald-700/50">
                        Live Telemetry
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-mono text-xs">
                    <!-- Origin Weather Card -->
                    <div class="bg-[#090D15] border border-[#1E293B] rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-slate-400 text-[11px] font-bold uppercase">Origin: {{ $shipment->origin->code }}</span>
                            <span class="text-[10px] text-cyan-400">{{ $originWeather['condition'] }}</span>
                        </div>
                        <div class="text-2xl font-black text-white mb-2">{{ $originWeather['temperature'] }}°C</div>
                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-400 pt-2 border-t border-slate-800">
                            <div>Humidity: <strong class="text-slate-200">{{ $originWeather['humidity'] }}%</strong></div>
                            <div>Wind: <strong class="text-slate-200">{{ $originWeather['wind_speed_kts'] }} Kts</strong></div>
                        </div>
                    </div>

                    <!-- Destination Weather Card -->
                    <div class="bg-[#090D15] border border-[#1E293B] rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-slate-400 text-[11px] font-bold uppercase">Destination: {{ $shipment->destination->code }}</span>
                            <span class="text-[10px] text-cyan-400">{{ $destWeather['condition'] }}</span>
                        </div>
                        <div class="text-2xl font-black text-white mb-2">{{ $destWeather['temperature'] }}°C</div>
                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-400 pt-2 border-t border-slate-800">
                            <div>Humidity: <strong class="text-slate-200">{{ $destWeather['humidity'] }}%</strong></div>
                            <div>Wind: <strong class="text-slate-200">{{ $destWeather['wind_speed_kts'] }} Kts</strong></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right 5 Cols: Cargo Manifest & Sensor Telemetry -->
        <div class="lg:col-span-5 space-y-5">
            
            <!-- Sensor Telemetry (Reefer or ADS-B) -->
            <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl font-mono">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase text-slate-300">
                        {{ $shipment->transport_mode === 'AIR' ? 'Flight Telemetry Sensors' : 'Reefer Cold-Chain Telemetry' }}
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-950 text-emerald-300 border border-emerald-700/50">
                        Calibrated
                    </span>
                </div>

                @if ($shipment->transport_mode === 'AIR')
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-[#090D15] border border-[#1E293B] rounded-xl p-3">
                        <span class="text-[10px] text-slate-400 block mb-1">Airspeed</span>
                        <div class="text-xl font-bold text-white">{{ $shipment->air_speed_kts ?? 485 }} Kts</div>
                        <span class="text-[10px] text-emerald-400">Ground Track 290°</span>
                    </div>
                    <div class="bg-[#090D15] border border-[#1E293B] rounded-xl p-3">
                        <span class="text-[10px] text-slate-400 block mb-1">Altitude</span>
                        <div class="text-xl font-bold text-cyan-400">FL{{ round(($shipment->air_altitude_ft ?? 36000) / 100) }}</div>
                        <span class="text-[10px] text-slate-400">{{ number_format($shipment->air_altitude_ft ?? 36000) }} ft MSL</span>
                    </div>
                </div>
                @else
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-[#090D15] border border-[#1E293B] rounded-xl p-3">
                        <span class="text-[10px] text-slate-400 block mb-1">Internal Temp</span>
                        <div class="text-xl font-bold text-white">{{ $shipment->temperature_celsius ?? 4.2 }}°C</div>
                        <span class="text-[10px] text-emerald-400 font-medium">±0.2°C Stabilized</span>
                    </div>
                    <div class="bg-[#090D15] border border-[#1E293B] rounded-xl p-3">
                        <span class="text-[10px] text-slate-400 block mb-1">Relative Humidity</span>
                        <div class="text-xl font-bold text-cyan-400">{{ $shipment->humidity_pct ?? 62 }}%</div>
                        <span class="text-[10px] text-cyan-400">Target Reached</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Cargo Item Manifest Table -->
            <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono font-bold uppercase text-slate-300 tracking-wider">Cargo Manifest Items</span>
                    <span class="text-xs font-mono text-cyan-400 font-bold">
                        {{ number_format($shipment->total_weight, 2) }} KG Total
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#1E293B] text-[10px] font-mono text-slate-400 uppercase">
                                <th class="py-2">Item Description</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Weight (KG)</th>
                                <th class="py-2 text-right">Volume (CBM)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#182338]">
                            @forelse($shipment->items as $item)
                            <tr>
                                <td class="py-2.5 font-semibold text-white">{{ $item->item_name }}</td>
                                <td class="py-2.5 text-center font-mono">{{ $item->quantity }}</td>
                                <td class="py-2.5 text-right font-mono text-slate-200">{{ number_format($item->weight_kg, 2) }}</td>
                                <td class="py-2.5 text-right font-mono text-slate-400">{{ number_format($item->volume_cbm, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-4 text-center text-slate-500">No items specified.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Operational Exceptions Log -->
            <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono font-bold uppercase text-slate-300 tracking-wider">Incidents &amp; Exceptions</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold {{ $shipment->exceptions->where('status', '!=', 'RESOLVED')->count() > 0 ? 'bg-amber-950 text-amber-300 border border-amber-700/50' : 'bg-emerald-950 text-emerald-300' }}">
                        {{ $shipment->exceptions->count() }} Logged
                    </span>
                </div>

                <div class="space-y-2.5 max-h-60 overflow-y-auto">
                    @forelse($shipment->exceptions as $ex)
                    <div class="p-3 bg-[#090D15] border border-[#1E293B] rounded-xl text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-amber-300 font-mono">{{ str_replace('_', ' ', $ex->exception_type) }}</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold {{ $ex->severity === 'HIGH' ? 'bg-red-950 text-red-300' : 'bg-amber-950 text-amber-300' }}">
                                {{ $ex->severity }}
                            </span>
                        </div>
                        <div class="text-slate-400 text-[11px] font-mono">
                            Detected: {{ $ex->detected_at->format('d M H:i') }} • Status: 
                            <strong class="{{ $ex->status === 'RESOLVED' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $ex->status }}</strong>
                        </div>
                        @if ($ex->resolution_notes)
                            <div class="text-[11px] text-slate-300 italic pt-1 border-t border-slate-800">
                                Notes: {{ $ex->resolution_notes }}
                            </div>
                        @endif
                        @if ($ex->status !== 'RESOLVED' && auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                            <form action="{{ route('exceptions.resolve', $ex) }}" method="POST" class="pt-2 flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="RESOLVED">
                                <input type="text" name="resolution_notes" required placeholder="Resolution notes..." 
                                       class="flex-1 bg-[#162235] border border-[#233550] rounded-lg px-2 py-1 text-[11px] text-white">
                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] rounded-lg">
                                    Resolve
                                </button>
                            </form>
                        @endif
                    </div>
                    @empty
                    <div class="p-4 text-center text-xs text-slate-500 font-mono">
                        No operational exceptions recorded.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- Tracking Events Chronological Timeline (Slide 7 Requirement) -->
    <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-white tracking-wide">Historical Tracking Timeline</h3>
                <p class="text-xs text-slate-400">Append-only audit trail of physical checkpoints, transshipments, and customs clearances</p>
            </div>
            <span class="font-mono text-xs text-cyan-400 font-semibold">
                {{ $shipment->trackingEvents->count() }} Events Recorded
            </span>
        </div>

        <!-- Vertical Timeline -->
        <div class="relative pl-6 sm:pl-8 space-y-6 before:content-[''] before:absolute before:left-2 sm:before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-[#233550]">
            @foreach($shipment->trackingEvents as $event)
            <div class="relative group">
                <!-- Timeline Dot Indicator -->
                <div class="absolute -left-6 sm:-left-8 top-1.5 w-4 h-4 rounded-full bg-[#121A28] border-2 border-emerald-400 group-hover:scale-125 transition flex items-center justify-center">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                </div>

                <!-- Event Card -->
                <div class="bg-[#090D15] border border-[#1E293B] rounded-xl p-4 hover:border-slate-700 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-cyan-400 uppercase">
                                {{ str_replace('_', ' ', $event->status) }}
                            </span>
                            @if ($event->location)
                                <span class="text-xs font-semibold text-white">
                                    • {{ $event->location->name }} ({{ $event->location->code }})
                                </span>
                            @endif
                        </div>
                        <span class="font-mono text-[11px] text-slate-400">
                            {{ $event->event_time->format('D, d M Y • H:i \U\T\C') }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ $event->description }}
                    </p>

                    <div class="mt-2 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[10px] font-mono text-slate-500">
                        <span>Logged by: {{ $event->creator->name ?? 'System Dispatch' }}</span>
                        @if ($event->latitude && $event->longitude)
                            <span>GPS: {{ $event->latitude }}, {{ $event->longitude }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Modal 1: Add Tracking Event (Slide 7) -->
<div id="modal-tracking" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#121A28] border border-[#233550] rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-[#1E293B] pb-3">
            <h3 class="text-sm font-bold text-white uppercase font-mono tracking-wider">Add Tracking Event</h3>
            <button onclick="document.getElementById('modal-tracking').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form action="{{ route('shipments.tracking.store', $shipment) }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-300 mb-1">New Shipment Status</label>
                <select name="status" required class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
                    <option value="IN_TRANSIT" selected>IN TRANSIT (Underway)</option>
                    <option value="BERTHED">BERTHED (At Berth / Gantry)</option>
                    <option value="IN_FLIGHT">IN FLIGHT (Air Cruising)</option>
                    <option value="PICKED_UP">PICKED UP</option>
                    <option value="DELIVERED">DELIVERED (Terminal Handover)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Checkpoint Location Node</label>
                <select name="location_id" class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
                    <option value="">Select location node (optional)...</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }}) - {{ $loc->country }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Event Timestamp</label>
                    <input type="datetime-local" name="event_time" value="{{ now()->format('Y-m-d\TH:i') }}" required
                           class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Corridor Stage (1-4)</label>
                    <input type="number" name="current_stage" min="1" max="4" value="{{ $shipment->current_stage }}"
                           class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Event Description &amp; Details</label>
                <textarea name="description" rows="3" required placeholder="e.g. Vessel cleared customs; pilot boarded for harbor transit..."
                          class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl p-2.5 text-white placeholder-slate-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-[#1E293B]">
                <button type="button" onclick="document.getElementById('modal-tracking').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow">Record Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Report Operational Exception (Slide 8) -->
<div id="modal-exception" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#121A28] border border-[#233550] rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-[#1E293B] pb-3">
            <h3 class="text-sm font-bold text-white uppercase font-mono tracking-wider flex items-center gap-2">
                <span class="text-amber-400">⚠️</span> Log Incident / Exception
            </h3>
            <button onclick="document.getElementById('modal-exception').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form action="{{ route('shipments.exceptions.store', $shipment) }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Exception Category (Slide 8 types)</label>
                <select name="exception_type" required class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
                    <option value="PORT_CONGESTION">PORT CONGESTION (Berth / Queue Hold)</option>
                    <option value="WEATHER_DELAY">WEATHER DELAY (Typhoon, Gale, Fog)</option>
                    <option value="CUSTOMS_HOLD">CUSTOMS HOLD (Inspection / Quarantine)</option>
                    <option value="DOCUMENTATION_ISSUE">DOCUMENTATION ISSUE (B/L / Manifest mismatch)</option>
                    <option value="DAMAGED">DAMAGED CARGO</option>
                    <option value="MISROUTED">MISROUTED SHIPMENT</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Severity Level</label>
                    <select name="severity" required class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
                        <option value="LOW">LOW (0 - 6 Hours)</option>
                        <option value="MEDIUM" selected>MEDIUM (6 - 24 Hours)</option>
                        <option value="HIGH">HIGH (> 24 Hours)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Estimated Impact (Hours)</label>
                    <input type="number" name="delay_impact_hours" min="0" max="720" value="24"
                           class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Detection Time</label>
                <input type="datetime-local" name="detected_at" value="{{ now()->format('Y-m-d\TH:i') }}" required
                       class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-[#1E293B]">
                <button type="button" onclick="document.getElementById('modal-exception').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow">Dispatch Exception Alert</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const originLat = {{ $shipment->origin->latitude }};
        const originLng = {{ $shipment->origin->longitude }};
        const destLat = {{ $shipment->destination->latitude }};
        const destLng = {{ $shipment->destination->longitude }};

        const map = L.map('shipment-map', {attributionControl: false}).setView([(originLat + destLat) / 2, (originLng + destLng) / 2], 3);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 18,
            subdomains: 'abcd',
        }).addTo(map);

        const route = [
            [originLat, originLng],
            @foreach($shipment->trackingEvents as $ev)
                @if ($ev->latitude && $ev->longitude)
                    [{{ $ev->latitude }}, {{ $ev->longitude }}],
                @endif
            @endforeach
            [destLat, destLng]
        ];

        L.polyline(route, {
            color: '{{ $shipment->transport_mode === 'AIR' ? '#22C55E' : '#38BDF8' }}',
            dashArray: '{{ $shipment->transport_mode === 'AIR' ? "5, 7" : "none" }}',
            weight: 3.5,
            opacity: 0.9
        }).addTo(map);

        L.marker([originLat, originLng]).addTo(map).bindPopup("<b>Origin: {{ $shipment->origin->name }}</b>");
        L.marker([destLat, destLng]).addTo(map).bindPopup("<b>Destination: {{ $shipment->destination->name }}</b>");

        @if ($latest = $shipment->latestTrackingEvent)
            @if ($latest->latitude && $latest->longitude)
                L.marker([{{ $latest->latitude }}, {{ $latest->longitude }}]).addTo(map)
                    .bindPopup("<b>Current Location</b><br>{{ $latest->description }}")
                    .openPopup();
            @endif
        @endif

        map.fitBounds(L.latLngBounds(route), {padding: [40, 40]});
    });
</script>
@endpush
