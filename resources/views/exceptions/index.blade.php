@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Title & Stats Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-400 animate-pulse"></span>
                    Operational Incident Desk
                </h1>
                <span class="px-2 py-0.5 rounded bg-red-950 text-red-300 border border-red-700/50 font-mono text-xs font-bold">
                    {{ $exceptions->total() }} Logged Incidents
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Prioritize, investigate, and resolve maritime holds, severe weather diversions, and port convoy congestions.
            </p>
        </div>

        <!-- Filter tabs -->
        <div class="flex items-center gap-2 font-mono text-xs">
            <a href="{{ route('exceptions.index', ['status' => '']) }}" class="px-3 py-1.5 rounded-xl border border-[#233550] transition {{ !request('status') ? 'bg-cyan-950 text-cyan-300 border-cyan-500' : 'bg-[#162235] text-slate-300' }}">
                All
            </a>
            <a href="{{ route('exceptions.index', ['status' => 'OPEN']) }}" class="px-3 py-1.5 rounded-xl border border-[#233550] transition {{ request('status') === 'OPEN' ? 'bg-red-950 text-red-300 border-red-500' : 'bg-[#162235] text-slate-300' }}">
                Open
            </a>
            <a href="{{ route('exceptions.index', ['status' => 'INVESTIGATING']) }}" class="px-3 py-1.5 rounded-xl border border-[#233550] transition {{ request('status') === 'INVESTIGATING' ? 'bg-amber-950 text-amber-300 border-amber-500' : 'bg-[#162235] text-slate-300' }}">
                Investigating
            </a>
            <a href="{{ route('exceptions.index', ['status' => 'RESOLVED']) }}" class="px-3 py-1.5 rounded-xl border border-[#233550] transition {{ request('status') === 'RESOLVED' ? 'bg-emerald-950 text-emerald-300 border-emerald-500' : 'bg-[#162235] text-slate-300' }}">
                Resolved
            </a>
        </div>
    </div>

    <!-- Incidents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($exceptions as $ex)
        <div class="bg-[#121A28] border {{ $ex->status === 'RESOLVED' ? 'border-[#1E293B]' : ($ex->severity === 'HIGH' ? 'border-red-600/50 shadow-red-900/10' : 'border-amber-600/40') }} rounded-2xl p-5 shadow-xl flex flex-col justify-between space-y-4">
            
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 rounded font-mono text-[10px] font-bold uppercase {{ $ex->severity === 'HIGH' ? 'bg-red-950 text-red-300 border border-red-700/60' : ($ex->severity === 'MEDIUM' ? 'bg-amber-950 text-amber-300 border border-amber-700/60' : 'bg-blue-950 text-blue-300 border border-blue-700/60') }}">
                        {{ $ex->severity }} Severity
                    </span>
                    <span class="font-mono text-xs {{ $ex->status === 'RESOLVED' ? 'text-emerald-400' : 'text-amber-400' }} font-bold">
                        ● {{ $ex->status }}
                    </span>
                </div>

                <h3 class="text-base font-bold text-white tracking-tight">
                    {{ str_replace('_', ' ', $ex->exception_type) }}
                </h3>

                <div class="mt-2 text-xs font-mono text-cyan-400">
                    Shipment: 
                    <a href="{{ route('shipments.show', $ex->shipment) }}" class="underline hover:text-cyan-300 font-bold">
                        {{ $ex->shipment->shipment_number }}
                    </a>
                </div>

                <div class="text-[11px] text-slate-400 mt-1">
                    Carrier: <strong class="text-slate-200">{{ $ex->shipment->carrier->name }}</strong>
                    • Route: <span class="font-mono">{{ $ex->shipment->origin->code }} &rarr; {{ $ex->shipment->destination->code }}</span>
                </div>

                @if ($ex->resolution_notes)
                <div class="mt-3 p-3 bg-[#090D15] border border-slate-800 rounded-xl text-xs text-slate-300 italic">
                    <strong class="text-slate-400 font-mono not-italic block text-[10px] uppercase mb-0.5">Resolution Notes:</strong>
                    {{ $ex->resolution_notes }}
                </div>
                @endif
            </div>

            <!-- Footer & Resolution Form -->
            <div class="pt-3 border-t border-[#1E293B] text-xs">
                <div class="flex items-center justify-between text-[11px] font-mono text-slate-500 mb-3">
                    <span>Detected: {{ $ex->detected_at->diffForHumans() }}</span>
                    <span>By: {{ $ex->reporter->name ?? 'System' }}</span>
                </div>

                @if ($ex->status !== 'RESOLVED' && auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                <form action="{{ route('exceptions.resolve', $ex) }}" method="POST" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="resolution_notes" required placeholder="Type corrective resolution action..."
                           class="w-full bg-[#090D15] border border-[#1E293B] rounded-lg px-2.5 py-1.5 text-xs text-white">
                    <div class="flex gap-2">
                        <button type="submit" name="status" value="INVESTIGATING" class="flex-1 py-1 bg-amber-700/60 hover:bg-amber-600 text-white font-bold rounded-lg text-[11px] transition">
                            Mark Investigating
                        </button>
                        <button type="submit" name="status" value="RESOLVED" class="flex-1 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-[11px] transition">
                            Resolve Case
                        </button>
                    </div>
                </form>
                @endif
            </div>

        </div>
        @empty
        <div class="col-span-full py-16 text-center text-slate-400 text-sm bg-[#121A28] border border-[#1F2C42] rounded-2xl">
            No incidents reported matching criteria. All shipping corridors running smoothly.
        </div>
        @endforelse
    </div>

    <div class="pt-4">
        {{ $exceptions->links() }}
    </div>

</div>
@endsection
