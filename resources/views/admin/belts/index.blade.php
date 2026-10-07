<x-admin-layout title="Belts">
    <x-slot name="header">Belts</x-slot>

    <x-admin.page-header title="Belts" description="Manage grading ranks used across belt tests and student progression.">
        <x-slot:actions>
            <x-admin.button :href="route('admin.belts.create')" variant="primary">Add belt</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" placeholder="Name or color" />
        <x-admin.input label="Status" name="status" type="select">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </x-admin.input>
    </x-admin.filters>

    <x-admin.card :padding="false">
        @if ($belts->isEmpty())
            <div class="p-6">
                <x-admin.empty-state title="No belts yet" description="Create the academy belt ranks before assigning students.">
                    <x-slot:actions>
                        <x-admin.button :href="route('admin.belts.create')">Create first belt</x-admin.button>
                    </x-slot:actions>
                </x-admin.empty-state>
            </div>
        @else
            <x-admin.table>
                <x-slot:head>
                    <th class="px-4 py-3">Rank</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Color</th>
                    <th class="px-4 py-3">Students</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </x-slot:head>

                @foreach ($belts as $belt)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $belt->rank_order }}</td>
                        <td class="px-4 py-3">{{ $belt->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full border border-slate-200" style="background: {{ $belt->color ?: '#cbd5e1' }}"></span>
                                {{ $belt->color ?: '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $belt->students_count }}</td>
                        <td class="px-4 py-3"><x-admin.badge :status="$belt->status" /></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <x-admin.button :href="route('admin.belts.edit', $belt)" variant="secondary">Edit</x-admin.button>
                                <x-admin.confirm-form
                                    :action="route('admin.belts.destroy', $belt)"
                                    title="Delete belt?"
                                    message="This permanently removes the belt if it is not assigned to students."
                                >
                                    <x-slot:trigger>
                                        <x-admin.button type="button" variant="danger">Delete</x-admin.button>
                                    </x-slot:trigger>
                                </x-admin.confirm-form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>

            <x-admin.pagination :paginator="$belts" />
        @endif
    </x-admin.card>
</x-admin-layout>
