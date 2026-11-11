@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 sm:p-6 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Register New Consignment Manifest</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Initialize global cargo corridor, assign carrier, schedule milestones, and attach freight manifest</p>
        </div>
        <a href="{{ route('shipments.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#1A273D] dark:hover:bg-[#233550] text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-[#233550] transition">
            &larr; Back to List
        </a>
    </div>

    <form action="{{ route('shipments.store') }}" method="POST" class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-6 sm:p-8 shadow-sm space-y-6 text-xs">
        @csrf

        <!-- Section 1: Route & Mode -->
        <div>
            <h2 class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-cyan-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-cyan-400"></span> 1. Corridor &amp; Transport Mode
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Transport Mode</label>
                    <select name="transport_mode" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="OCEAN" selected>OCEAN (Container Vessel)</option>
                        <option value="AIR">AIR (Express Airway)</option>
                        <option value="ROAD">ROAD (Inland Heavy Haul)</option>
                        <option value="MULTIMODAL">MULTIMODAL (Combined Sea + Air)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Origin Port / Gateway</label>
                    <select name="origin_id" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="">Select origin node...</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->code }}) - {{ $l->country }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Destination Port / Hub</label>
                    <select name="destination_id" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="">Select destination node...</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->code }}) - {{ $l->country }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Carrier & Customer -->
        <div class="pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            <h2 class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-cyan-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-cyan-400"></span> 2. Logistics Operator &amp; Customer
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Assigned Carrier Line</label>
                    <select name="carrier_id" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        @foreach($carriers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Account / Client</label>
                    <select name="customer_id" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Consignee Name</label>
                    <input type="text" name="consignee_name" required placeholder="PT Samudera or Apex Hub"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Custom B/L or AWB # (Optional)</label>
                    <input type="text" name="shipment_number" placeholder="Leave blank to auto-generate"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Vessel Name / Flight #</label>
                    <input type="text" name="vessel_or_flight" placeholder="e.g. Maersk Mc-Kinney Moller or QR-803F"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Initial Status</label>
                    <select name="status" class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="CREATED">CREATED</option>
                        <option value="BOOKED" selected>BOOKED</option>
                        <option value="PICKED_UP">PICKED UP</option>
                        <option value="IN_TRANSIT">IN TRANSIT</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 3: Schedule & Sensors -->
        <div class="pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            <h2 class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-cyan-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-cyan-400"></span> 3. Schedule Milestones &amp; Sensor Telemetry
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Expected Departure</label>
                    <input type="datetime-local" name="expected_departure" value="{{ now()->format('Y-m-d\TH:i') }}" required
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Expected Arrival (ETA)</label>
                    <input type="datetime-local" name="expected_arrival" value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}" required
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Berth / Gate Number</label>
                    <input type="text" name="berth_number" placeholder="e.g. Berth 402"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Reefer Temp (°C)</label>
                    <input type="number" step="0.1" name="temperature_celsius" placeholder="e.g. 4.2"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Reefer Humidity (%)</label>
                    <input type="number" step="0.1" name="humidity_pct" placeholder="e.g. 62.0"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Section 4: Cargo Items Manifest -->
        <div class="pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            <h2 class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-cyan-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-cyan-400"></span> 4. Primary Cargo Item Manifest
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Cargo Description</label>
                    <input type="text" name="items[0][item_name]" value="Commercial High-Priority Payload" required
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Weight (KG)</label>
                    <input type="number" step="0.01" name="items[0][weight_kg]" value="12500.00" required
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Volume (CBM)</label>
                    <input type="number" step="0.01" name="items[0][volume_cbm]" value="32.50"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md transition">
                Create &amp; Dispatch Consignment
            </button>
        </div>

    </form>

</div>
@endsection
