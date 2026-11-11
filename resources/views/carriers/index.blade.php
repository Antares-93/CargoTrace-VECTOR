@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
        <div>
            <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                Partner Carrier Registry
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Authorized ocean shipping lines, air freight operators, and intermodal transport partners.
            </p>
        </div>

        @if (auth()->check() && auth()->user()->isAdmin())
        <button onclick="document.getElementById('modal-carrier').classList.remove('hidden')" 
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Carrier</span>
        </button>
        @endif
    </div>

    <!-- Carriers Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($carriers as $c)
        <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 bg-cyan-950 text-cyan-300 border border-cyan-700/50 rounded-lg font-mono font-bold text-xs">
                        {{ $c->code }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold {{ $c->is_active ? 'bg-emerald-950 text-emerald-300 border border-emerald-700/50' : 'bg-red-950 text-red-300' }}">
                        {{ $c->is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                </div>

                <h3 class="text-base font-bold text-white tracking-tight">{{ $c->name }}</h3>
                
                <div class="mt-3 space-y-1 text-xs text-slate-400 font-mono">
                    <div>Email: <span class="text-slate-200">{{ $c->contact_email ?? 'N/A' }}</span></div>
                    <div>Phone: <span class="text-slate-200">{{ $c->contact_phone ?? 'N/A' }}</span></div>
                </div>
            </div>

            <div class="pt-3 border-t border-[#1E293B] flex items-center justify-between text-xs font-mono">
                <span class="text-slate-400">Shipments: <strong class="text-cyan-400">{{ $c->shipments_count }}</strong></span>
                
                @if (auth()->check() && auth()->user()->isAdmin())
                <form action="{{ route('carriers.toggle', $c) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[11px] {{ $c->is_active ? 'text-red-400 hover:underline' : 'text-emerald-400 hover:underline' }}">
                        {{ $c->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- Modal: Add Carrier -->
<div id="modal-carrier" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#121A28] border border-[#233550] rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-[#1E293B] pb-3">
            <h3 class="text-sm font-bold text-white uppercase font-mono tracking-wider">Register Partner Carrier</h3>
            <button onclick="document.getElementById('modal-carrier').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form action="{{ route('carriers.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Carrier Code (e.g. MSK, EVG, QTR)</label>
                <input type="text" name="code" required placeholder="MSK" maxlength="10"
                       class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white font-mono uppercase">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Company / Carrier Name</label>
                <input type="text" name="name" required placeholder="Maersk Line"
                       class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Operations Email</label>
                <input type="email" name="contact_email" placeholder="dispatch@carrier.com"
                       class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Operations Phone</label>
                <input type="text" name="contact_phone" placeholder="+1 800 555 0199"
                       class="w-full bg-[#090D15] border border-[#1E293B] rounded-xl px-3 py-2 text-white">
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-[#1E293B]">
                <button type="button" onclick="document.getElementById('modal-carrier').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow">Save Carrier</button>
            </div>
        </form>
    </div>
</div>
@endsection
