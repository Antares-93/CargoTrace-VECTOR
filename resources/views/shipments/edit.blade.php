@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 sm:p-6 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Edit Consignment: {{ $shipment->shipment_number }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Update voyage schedule, arrival forecasts, destination nodes, or sensor telemetry parameters</p>
        </div>
        <a href="{{ route('shipments.show', $shipment) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#1A273D] dark:hover:bg-[#233550] text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-[#233550] transition">
            &larr; View Manifest
        </a>
    </div>

    <form action="{{ route('shipments.update', $shipment) }}" method="POST" class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-6 sm:p-8 shadow-sm space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- Section 1: Carrier & Destination -->
        <div>
            <h2 class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-cyan-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-cyan-400"></span> 1. Routing &amp; Carrier Assignment
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Carrier Line</label>
                    <select name="carrier_id" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        @foreach($carriers as $c)
                            <option value="{{ $c->id }}" {{ $shipment->carrier_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Origin Node</label>
                    <select name="origin_id" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" {{ $shipment->origin_id == $l->id ? 'selected' : '' }}>
                                {{ $l->name }} ({{ $l->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Destination Node</label>
                    <select name="destination_id" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" {{ $shipment->destination_id == $l->id ? 'selected' : '' }}>
                                {{ $l->name }} ({{ $l->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Mode & Craft -->
        <div class="pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            <h2 class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-cyan-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-cyan-400"></span> 2. Transport Details &amp; Consignee
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Transport Mode</label>
                    <select name="transport_mode" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="OCEAN" {{ $shipment->transport_mode === 'OCEAN' ? 'selected' : '' }}>OCEAN</option>
                        <option value="AIR" {{ $shipment->transport_mode === 'AIR' ? 'selected' : '' }}>AIR</option>
                        <option value="ROAD" {{ $shipment->transport_mode === 'ROAD' ? 'selected' : '' }}>ROAD</option>
                        <option value="MULTIMODAL" {{ $shipment->transport_mode === 'MULTIMODAL' ? 'selected' : '' }}>MULTIMODAL</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Consignee Name</label>
                    <input type="text" name="consignee_name" value="{{ old('consignee_name', $shipment->consignee_name) }}" required
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Vessel or Flight #</label>
                    <input type="text" name="vessel_or_flight" value="{{ old('vessel_or_flight', $shipment->vessel_or_flight) }}"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
                </div>
            </div>
        </div>

        <!-- Section 3: Schedule & ETA -->
        <div class="pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            <h2 class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-cyan-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-cyan-400"></span> 3. Schedule &amp; ETA Calculation
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Expected Departure</label>
                    <input type="datetime-local" name="expected_departure" value="{{ $shipment->expected_departure->format('Y-m-d\TH:i') }}" required
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Expected Arrival</label>
                    <input type="datetime-local" name="expected_arrival" value="{{ $shipment->expected_arrival->format('Y-m-d\TH:i') }}" required
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Estimated Arrival (triggers delay rule)</label>
                    <input type="datetime-local" name="estimated_arrival" value="{{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('Y-m-d\TH:i') : '' }}"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Section 4: Telemetry -->
        <div class="pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            <h2 class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-cyan-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-cyan-400"></span> 4. Telemetry &amp; Terminal Berth
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Berth Number</label>
                    <input type="text" name="berth_number" value="{{ old('berth_number', $shipment->berth_number) }}"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Reefer Temp (°C)</label>
                    <input type="number" step="0.1" name="temperature_celsius" value="{{ old('temperature_celsius', $shipment->temperature_celsius) }}"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Reefer Humidity (%)</label>
                    <input type="number" step="0.1" name="humidity_pct" value="{{ old('humidity_pct', $shipment->humidity_pct) }}"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
                </div>
            </div>

            <div class="mt-3">
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Special Handling Notes</label>
                <textarea name="notes" rows="2" class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl p-2.5 text-slate-900 dark:text-white">{{ old('notes', $shipment->notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            <a href="{{ route('shipments.show', $shipment) }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md transition">
                Save &amp; Update Manifest
            </button>
        </div>

    </form>

</div>
@endsection
