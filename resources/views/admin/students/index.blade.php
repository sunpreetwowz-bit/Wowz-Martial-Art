<x-admin-layout title="Students">
    <x-slot name="header">Students</x-slot>

    <x-admin.page-header title="Students" description="Create and manage student accounts. Students cannot self-register.">
        <x-slot:actions>
            <x-admin.button :href="route('admin.students.create')" variant="primary">Add student</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" placeholder="Name, email, ID, phone" />
        <x-admin.input label="Status" name="status" type="select">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-admin.input>
        <x-admin.input label="Belt" name="belt_id" type="select">
            <option value="">All belts</option>
            @foreach ($belts as $belt)
                <option value="{{ $belt->id }}" @selected((string) request('belt_id') === (string) $belt->id)>{{ $belt->name }}</option>
            @endforeach
        </x-admin.input>
    </x-admin.filters>

    <x-admin.card :padding="false">
        @if ($students->isEmpty())
            <div class="p-6">
                <x-admin.empty-state title="No students found" description="Create a student account to give them dashboard access.">
                    <x-slot:actions>
                        <x-admin.button :href="route('admin.students.create')">Add student</x-admin.button>
                    </x-slot:actions>
                </x-admin.empty-state>
            </div>
        @else
            <x-admin.table>
                <x-slot:head>
                    <th class="px-4 py-3">Student</th>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Belt</th>
                    <th class="px-4 py-3">Service</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </x-slot:head>

                @foreach ($students as $student)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">{{ $student->user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $student->user->email }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $student->student_code }}</td>
                        <td class="px-4 py-3">{{ $student->currentBelt?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $student->primaryService?->name ?? '—' }}</td>
                        <td class="px-4 py-3"><x-admin.badge :status="$student->status" /></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <x-admin.button :href="route('admin.students.show', $student)" variant="secondary">View</x-admin.button>
                                <x-admin.button :href="route('admin.students.edit', $student)" variant="ghost">Edit</x-admin.button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>

            <x-admin.pagination :paginator="$students" />
        @endif
    </x-admin.card>
</x-admin-layout>
