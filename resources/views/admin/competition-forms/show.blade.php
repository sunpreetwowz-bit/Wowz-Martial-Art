<x-admin-layout title="Competition Form">
    <x-slot name="header">Competition Form</x-slot>
    <x-admin.page-header :title="$form->title" :description="$form->description">
        <x-slot name="actions">
            <x-admin.button :href="route('admin.competition-forms.download', $form)" variant="secondary">Download PDF</x-admin.button>
            <x-admin.button :href="route('admin.competition-forms.edit', $form)">Edit</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-admin.badge :status="$form->status" /></dd></div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Deadline</dt>
                    <dd @class(['font-medium text-red-700' => $form->isDeadlinePassed()])>
                        {{ optional($form->deadline_at)->format('d M Y H:i') ?: '—' }}
                        @if ($form->isDeadlinePassed())
                            <span class="block text-xs">Passed</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between"><dt class="text-slate-500">Created by</dt><dd>{{ $form->creator?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Assigned</dt><dd>{{ $form->assignments->where('status', \App\Enums\AssignmentStatus::Assigned)->count() }}</dd></div>
                @if ($form->google_form_url)
                    <div>
                        <dt class="text-slate-500">Google Form</dt>
                        <dd class="mt-1 break-all">
                            <a href="{{ $form->google_form_url }}" class="text-red-700 hover:underline" target="_blank" rel="noopener">Open form link</a>
                        </dd>
                    </div>
                @endif
            </dl>

            <form method="POST" action="{{ route('admin.competition-forms.destroy', $form) }}" class="mt-6 border-t border-slate-200 pt-6" onsubmit="return confirm('Delete this competition form?');">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" variant="danger">Delete form</x-admin.button>
            </form>
        </x-admin.card>

        <x-admin.card class="lg:col-span-2" :padding="false">
            <div class="border-b border-slate-100 px-4 py-3 text-sm font-semibold">Assignments</div>
            <x-admin.table>
                <x-slot:head>
                    <th class="px-4 py-3">Student</th>
                    <th class="px-4 py-3">Viewed</th>
                    <th class="px-4 py-3">Downloaded</th>
                    <th class="px-4 py-3">Response</th>
                    <th class="px-4 py-3">Status</th>
                </x-slot:head>
                @forelse ($form->assignments as $assignment)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $assignment->student->user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $assignment->student->student_code }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ optional($assignment->viewed_at)->format('d M H:i') ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm">{{ optional($assignment->downloaded_at)->format('d M H:i') ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($assignment->status === \App\Enums\AssignmentStatus::Assigned)
                                <form method="POST" action="{{ route('admin.competition-forms.assignments.update', [$form, $assignment]) }}" class="flex items-center gap-2">
                                    @csrf
                                    <select name="response_status" class="rounded-lg border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" onchange="this.form.submit()">
                                        @foreach ($responseStatuses as $status)
                                            <option value="{{ $status->value }}" @selected($assignment->response_status === $status)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <x-admin.badge :status="$assignment->response_status ?? 'pending'" />
                            @endif
                        </td>
                        <td class="px-4 py-3"><x-admin.badge :status="$assignment->status" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No students assigned.</td>
                    </tr>
                @endforelse
            </x-admin.table>
        </x-admin.card>
    </div>
</x-admin-layout>
