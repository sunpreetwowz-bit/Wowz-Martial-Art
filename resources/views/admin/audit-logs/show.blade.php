<x-admin-layout title="Audit Log">
    <x-slot name="header">Audit Log</x-slot>
    <x-admin.page-header :title="$log->action" :description="$log->created_at->format('d M Y H:i:s')" />

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Actor</dt><dd>{{ $log->user?->name ?? 'System' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd>{{ $log->user?->email ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">IP</dt><dd class="font-mono text-xs">{{ $log->ip_address ?: '—' }}</dd></div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Subject</dt>
                    <dd class="text-right text-xs">
                        @if ($log->subject_type)
                            {{ $log->subject_type }} #{{ $log->subject_id }}
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </dl>
            @if ($log->user_agent)
                <p class="mt-4 break-all text-xs text-slate-500">{{ $log->user_agent }}</p>
            @endif
        </x-admin.card>

        <x-admin.card class="lg:col-span-2">
            <h3 class="text-sm font-semibold">Old values</h3>
            <pre class="mt-2 overflow-x-auto rounded bg-slate-50 p-3 text-xs">{{ $log->old_values ? json_encode($log->old_values, JSON_PRETTY_PRINT) : '—' }}</pre>

            <h3 class="mt-6 text-sm font-semibold">New values</h3>
            <pre class="mt-2 overflow-x-auto rounded bg-slate-50 p-3 text-xs">{{ $log->new_values ? json_encode($log->new_values, JSON_PRETTY_PRINT) : '—' }}</pre>

            <div class="mt-6">
                <x-admin.button :href="route('admin.audit-logs.index')" variant="secondary">Back to list</x-admin.button>
            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
