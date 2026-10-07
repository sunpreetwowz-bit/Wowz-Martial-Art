<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::query()->with('user');

        if ($request->input('action')) {
            $query->where('action', 'like', '%'.$request->input('action').'%');
        }

        if ($request->input('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->input('q')) {
            $search = '%'.$request->input('q').'%';

            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', $search)
                    ->orWhere('subject_type', 'like', $search)
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        $logs = $query->latest()->paginate(30);

        return view('admin.audit-logs.index', [
            'logs' => $logs,
        ]);
    }

    public function show(AuditLog $audit_log)
    {
        $this->authorize('view', $audit_log);

        $audit_log->load('user');

        return view('admin.audit-logs.show', [
            'log' => $audit_log,
        ]);
    }
}
