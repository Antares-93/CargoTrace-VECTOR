@extends('layouts.app')

@section('content')
<div class="space-y-5">
    
    <!-- Top Action & Title Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-extrabold text-white tracking-tight">Global Consignments Directory</h1>
                <span class="px-2 py-0.5 rounded bg-emerald-950 text-emerald-300 border border-emerald-700/50 font-mono text-xs font-bold">
                    {{ $shipments->total() }} Active
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Real-time tracking manifest for sea lines, air express, and multimodal logistics corridors.
            </p>
        </div>

        @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
        <a href="{{ route('shipments.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-700/20 flex items-center gap-2 transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Register New Shipment</span>
        </a>
        @endif
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-4 shadow-lg flex flex-col md:flex-row items-center justify-between gap-3">
        
        <form action="{{ route('shipments.index') }}" method="GET" class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5">
            <!-- Search -->
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search identifier, consignee..."
                       class="w-full bg-[#0E1522] border border-[#1F2C42] rounded-xl px-3 py-2 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Mode Filter -->
            <select name="mode" class="bg-[#0E1522] border border-[#1F2C42] rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="all">All Transport Modes</option>
                <option value="OCEAN" {{ request('mode') === 'OCEAN' ? 'selected' : '' }}>Ocean Lines</option>
                <option value="AIR" {{ request('mode') === 'AIR' ? 'selected' : '' }}>Air Express</option>
                <option value="ROAD" {{ request('mode') === 'ROAD' ? 'selected' : '' }}>Road Freight</option>
            </select>

            <!-- Status Filter -->
            <select name="status" class="bg-[#0E1522] border border-[#1F2C42] rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="all">All Statuses</option>
                <option value="CREATED" {{ request('status') === 'CREATED' ? 'selected' : '' }}>Created</option>
                <option value="BOOKED" {{ request('status') === 'BOOKED' ? 'selected' : '' }}>Booked</option>
                <option value="PICKED_UP" {{ request('status') === 'PICKED_UP' ? 'selected' : '' }}>Picked Up</option>
                <option value="IN_TRANSIT" {{ request('status') === 'IN_TRANSIT' ? 'selected' : '' }}>In Transit</option>
                <option value="IN_FLIGHT" {{ request('status') === 'IN_FLIGHT' ? 'selected' : '' }}>In Flight</option>
                <option value="BERTHED" {{ request('status') === 'BERTHED' ? 'selected' : '' }}>Berthed</option>
                <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>Delivered</option>
            </select>

            <!-- Carrier Filter -->
            <div class="flex items-center gap-2">
                <select name="carrier_id" class="w-full bg-[#0E1522] border border-[#1F2C42] rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Carriers</option>
                    @foreach($carriers as $carrier)
                        <option value="{{ $carrier->id }}" {{ request('carrier_id') == $carrier->id ? 'selected' : '' }}>
                            {{ $carrier->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-[#1A273D] hover:bg-emerald-600 text-white rounded-xl text-xs font-semibold border border-[#233550] transition">
                    Filter
                </button>
            </div>
        </form>

    </div>

    <!-- Consignments Table -->
    <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#1E293B] text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-3">Tracking ID</th>
                        <th class="py-3 px-3">Mode</th>
                        <th class="py-3 px-3">Customer / Consignee</th>
                        <th class="py-3 px-3">Carrier &amp; Vessel/Flight</th>
                        <th class="py-3 px-3">Route Corridor</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">ETA &amp; Delay Impact</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#182338]">
                    @forelse($shipments as $s)
                    <tr class="hover:bg-[#162338] transition">
                        
                        <td class="py-3.5 px-3 font-mono font-bold text-cyan-400">
                            <a href="{{ route('shipments.show', $s) }}" class="hover:underline">
                                {{ $s->shipment_number }}
                            </a>
                        </td>

                        <td class="py-3.5 px-3">
                            @if ($s->transport_mode === 'AIR')
                                <span class="px-2 py-0.5 rounded bg-emerald-950 text-emerald-300 border border-emerald-700/50 font-mono text-[10px] font-bold">AIR</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-cyan-950 text-cyan-300 border border-cyan-700/50 font-mono text-[10px] font-bold">OCEAN</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-3">
                            <div class="font-bold text-white">{{ $s->consignee_name ?? $s->customer->name }}</div>
                            <div class="text-[10px] text-slate-400">Account: {{ $s->customer->email }}</div>
                        </td>

                        <td class="py-3.5 px-3">
                            <div class="text-slate-200 font-semibold">{{ $s->carrier->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $s->vessel_or_flight ?? 'Unassigned' }}</div>
                        </td>

                        <td class="py-3.5 px-3 font-mono">
                            <div class="text-white font-bold">{{ $s->origin->code }} &rarr; {{ $s->destination->code }}</div>
                            <div class="text-[10px] text-slate-400 font-sans truncate max-w-[170px]">{{ $s->origin->name }}</div>
                        </td>

                        <td class="py-3.5 px-3 font-mono">
                            @php
                                $statusClasses = match($s->status) {
                                    'IN_TRANSIT' => 'bg-cyan-950 text-cyan-300 border-cyan-700/50',
                                    'IN_FLIGHT' => 'bg-emerald-950 text-emerald-300 border-emerald-700/50',
                                    'BERTHED' => 'bg-teal-950 text-teal-300 border-teal-700/50',
                                    'DELIVERED' => 'bg-purple-950 text-purple-300 border-purple-700/50',
                                    default => 'bg-slate-800 text-slate-300 border-slate-700',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded border text-[10px] font-bold {{ $statusClasses }}">
                                {{ str_replace('_', ' ', $s->status) }}
                            </span>
                        </td>

                        <td class="py-3.5 px-3 font-mono">
                            <div class="text-slate-200">{{ $s->estimated_arrival ? $s->estimated_arrival->format('d M H:i') : $s->expected_arrival->format('d M H:i') }}</div>
                            @if ($s->delay_hours > 0)
                                <span class="text-amber-400 text-[10px] font-bold">+{{ $s->delay_hours }}h Delay ({{ $s->delay_status }})</span>
                            @else
                                <span class="text-emerald-400 text-[10px] font-bold">On Schedule</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('shipments.show', $s) }}" class="px-2.5 py-1 bg-[#1A273D] hover:bg-emerald-600 hover:text-white text-slate-300 text-xs font-semibold rounded-lg border border-[#233550] transition">
                                    Track
                                </a>
                                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                                <a href="{{ route('shipments.edit', $s) }}" class="px-2 py-1 bg-[#1A273D] hover:bg-cyan-600 hover:text-white text-slate-400 text-xs rounded-lg border border-[#233550] transition" title="Edit Parameters">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                            No shipments found matching the query.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-4 border-t border-[#1E293B]">
            {{ $shipments->links() }}
        </div>
    </div>

</div>
@endsection
