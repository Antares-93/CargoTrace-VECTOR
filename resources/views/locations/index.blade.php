@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 sm:p-6 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                Global Hubs &amp; Terminal Nodes
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Strategic seaports, air freight gateways, and transit choke points (Suez, Singapore, Rotterdam).
            </p>
        </div>

        @if (auth()->check() && auth()->user()->isAdmin())
        <button onclick="document.getElementById('modal-location').classList.remove('hidden')" 
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add Location Node</span>
        </button>
        @endif
    </div>

    <!-- Table of Nodes -->
    <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-[#1E293B] text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-3">UN/LOCODE</th>
                        <th class="py-3 px-3">Hub Name</th>
                        <th class="py-3 px-3">Category</th>
                        <th class="py-3 px-3">Country</th>
                        <th class="py-3 px-3">Coordinates (Lat, Lng)</th>
                        <th class="py-3 px-3 text-right">Associated Shipments</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#182338]">
                    @foreach($locations as $loc)
                    <tr class="hover:bg-slate-50 dark:hover:bg-[#162338] transition font-mono">
                        <td class="py-3 px-3 font-bold text-cyan-600 dark:text-cyan-400">{{ $loc->code }}</td>
                        <td class="py-3 px-3 font-sans font-semibold text-slate-800 dark:text-white">{{ $loc->name }}</td>
                        <td class="py-3 px-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $loc->type === 'SEAPORT' ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300' : ($loc->type === 'AIRPORT' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300') }}">
                                {{ $loc->type }}
                            </span>
                        </td>
                        <td class="py-3 px-3 font-sans text-slate-700 dark:text-slate-200">{{ $loc->country }}</td>
                        <td class="py-3 px-3 text-slate-500 dark:text-slate-400">{{ $loc->latitude }}, {{ $loc->longitude }}</td>
                        <td class="py-3 px-3 text-right font-bold text-slate-800 dark:text-slate-200">
                            {{ $loc->shipments_as_origin_count + $loc->shipments_as_destination_count }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Add Location -->
<div id="modal-location" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#121A28] border border-slate-200 dark:border-[#233550] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-[#1E293B] pb-3">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase font-mono tracking-wider">Register Location Node</h3>
            <button onclick="document.getElementById('modal-location').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl">&times;</button>
        </div>

        <form action="{{ route('locations.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Node Code (UN/LOCODE e.g. IDTPP, NLRTM)</label>
                <input type="text" name="code" required placeholder="IDTPP" maxlength="10"
                       class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono uppercase">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Hub Name</label>
                <input type="text" name="name" required placeholder="Tanjung Priok Container Terminal"
                       class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Type</label>
                    <select name="type" required class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="SEAPORT" selected>SEAPORT</option>
                        <option value="AIRPORT">AIRPORT</option>
                        <option value="CANAL">CANAL</option>
                        <option value="INLAND_HUB">INLAND HUB</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Country</label>
                    <input type="text" name="country" required placeholder="Indonesia"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Latitude</label>
                    <input type="number" step="0.000001" name="latitude" required placeholder="-6.1017"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Longitude</label>
                    <input type="number" step="0.000001" name="longitude" required placeholder="106.8833"
                           class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-[#1E293B]">
                <button type="button" onclick="document.getElementById('modal-location').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow">Save Node</button>
            </div>
        </form>
    </div>
</div>
@endsection
