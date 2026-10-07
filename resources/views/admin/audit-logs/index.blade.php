<x-admin-layout title="Audit Logs">
    <x-slot name="header">Audit Logs</x-slot>
    <x-admin.page-header title="Audit Logs" description="Administrative trail of sensitive actions. Not visible to students." />

    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" placeholder="Action, actor, or subject" />
        <x-admin.input label="Action" name="action" :value="request('action')" placeholder="e.g. payment.verified" />
    </x-admin.filters>

    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">When</th>
                <th class="px-4 py-3">Actor</th>
                <th class="px-4 py-3">Action</th>
                <th class="px-4 py-3">Subject</th>
                <th class="px-4 py-3 text-right">Details</th>
            </x-slot:head>
            @forelse ($logs as $log)
                <tr>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $log->action }}</td>
                    <td class="px-4 py-3 text-xs text-slate-600">
                        @if ($log->subject_type)
                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <x-admin.button :href="route('admin.audit-logs.show', $log)" variant="secondary">View</x-admin.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No audit events yet.</td>
                </tr>
            @endforelse
        </x-admin.table>
        <x-admin.pagination :paginator="$logs" />
    </x-admin.card>
</x-admin-layout>
