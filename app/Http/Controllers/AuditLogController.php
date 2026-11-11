<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest();

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        if ($entity = $request->input('entity_type')) {
            $query->where('entity_type', $entity);
        }

        $auditLogs = $query->paginate(20)->withQueryString();

        return view('audit_logs.index', compact('auditLogs'));
    }
}
