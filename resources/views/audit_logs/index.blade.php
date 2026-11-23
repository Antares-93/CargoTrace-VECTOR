@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 sm:p-6 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span data-i18n="audit_title">System Security &amp; Activity Audit Trail</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                <span data-i18n="audit_sub">Immutable chronological log of all consignment changes, tracking updates, and incident resolutions.</span>
            </p>
        </div>
        <span class="px-3 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700/50 rounded-xl font-mono font-bold text-xs">
            <span>{{ $auditLogs->total() }}</span> <span data-i18n="audit_logged_entries">Logged Entries</span>
        </span>
    </div>

    <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-[#1E293B] text-[10px] text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-3" data-i18n="audit_col_time">Timestamp</th>
                        <th class="py-3 px-3" data-i18n="audit_col_user">Actor / User</th>
                        <th class="py-3 px-3" data-i18n="audit_col_action">Action</th>
                        <th class="py-3 px-3" data-i18n="audit_col_entity">Entity</th>
                        <th class="py-3 px-3" data-i18n="audit_col_ip">IP Address</th>
                        <th class="py-3 px-3" data-i18n="audit_col_changes">Changes / Snapshot</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#182338]">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-slate-50 dark:hover:bg-[#162338] transition">
                        <td class="py-3 px-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-3 px-3 text-slate-900 dark:text-white font-sans font-semibold">
                            {{ $log->user->name ?? 'System Service' }}
                            @if ($log->user)
                                <span class="text-[10px] text-slate-400 block font-mono">({{ $log->user->role }})</span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-[#1A273D] text-cyan-700 dark:text-cyan-300 border border-slate-200 dark:border-[#233550]">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-slate-700 dark:text-slate-200">
                            {{ $log->entity_type }} #{{ $log->entity_id }}
                        </td>
                        <td class="py-3 px-3 text-slate-500 dark:text-slate-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        <td class="py-3 px-3 font-sans text-[11px] text-slate-600 dark:text-slate-300 max-w-xs truncate">
                            @if ($log->new_values)
                                <code class="text-[10px] text-emerald-700 dark:text-emerald-400 font-mono">{{ json_encode($log->new_values) }}</code>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 font-sans"><span data-i18n="audit_no_events">No audit events recorded yet.</span></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-[#1E293B]">
            {{ $auditLogs->links() }}
        </div>
    </div>

</div>
@endsection
