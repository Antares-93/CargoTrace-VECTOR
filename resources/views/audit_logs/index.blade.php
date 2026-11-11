@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
        <div>
            <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                System Security &amp; Activity Audit Trail
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Immutable chronological log of all consignment changes, tracking updates, and incident resolutions.
            </p>
        </div>
        <span class="px-2.5 py-1 bg-cyan-950 text-cyan-300 border border-cyan-700/50 rounded-lg font-mono font-bold text-xs">
            {{ $auditLogs->total() }} Logged Entries
        </span>
    </div>

    <div class="bg-[#121A28] border border-[#1F2C42] rounded-2xl p-5 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead>
                    <tr class="border-b border-[#1E293B] text-[10px] text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-3">Timestamp</th>
                        <th class="py-3 px-3">Actor / User</th>
                        <th class="py-3 px-3">Action</th>
                        <th class="py-3 px-3">Entity</th>
                        <th class="py-3 px-3">IP Address</th>
                        <th class="py-3 px-3">Changes / Snapshot</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#182338]">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-[#162338] transition">
                        <td class="py-3 px-3 text-slate-400 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-3 px-3 text-white font-sans font-semibold">
                            {{ $log->user->name ?? 'System Service' }}
                            @if ($log->user)
                                <span class="text-[10px] text-slate-400 block font-mono">({{ $log->user->role }})</span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#1A273D] text-cyan-300 border border-[#233550]">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-slate-200">
                            {{ $log->entity_type }} #{{ $log->entity_id }}
                        </td>
                        <td class="py-3 px-3 text-slate-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        <td class="py-3 px-3 font-sans text-[11px] text-slate-300 max-w-xs truncate">
                            @if ($log->new_values)
                                <code class="text-[10px] text-emerald-400 font-mono">{{ json_encode($log->new_values) }}</code>
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 font-sans">
                            No audit trail logs recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-3 border-t border-[#1E293B]">
            {{ $auditLogs->links() }}
        </div>
    </div>

</div>
@endsection
