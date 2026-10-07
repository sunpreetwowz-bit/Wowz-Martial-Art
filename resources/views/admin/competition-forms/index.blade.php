<x-admin-layout title="Competition Forms">
    <x-slot name="header">Competition Forms</x-slot>
    <x-admin.page-header title="Competition Forms" description="Upload PDF registration forms and assign them to selected students.">
        <x-slot name="actions">
            <x-admin.button :href="route('admin.competition-forms.create')">Upload form</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" placeholder="Title" />
        <x-admin.input label="Status" name="status" type="select">
            <option value="">All</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-admin.input>
    </x-admin.filters>

    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Deadline</th>
                <th class="px-4 py-3">Assigned</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @forelse ($forms as $form)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $form->title }}</td>
                    <td class="px-4 py-3">
                        @if ($form->deadline_at)
                            <span @class(['text-red-700' => $form->isDeadlinePassed()])>
                                {{ $form->deadline_at->format('d M Y H:i') }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $form->assigned_count }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$form->status" /></td>
                    <td class="px-4 py-3 text-right">
                        <x-admin.button :href="route('admin.competition-forms.show', $form)" variant="secondary">View</x-admin.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No competition forms yet.</td>
                </tr>
            @endforelse
        </x-admin.table>
        <x-admin.pagination :paginator="$forms" />
    </x-admin.card>
</x-admin-layout>
