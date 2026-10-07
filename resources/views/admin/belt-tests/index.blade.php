<x-admin-layout title="Belt Tests">
    <x-slot name="header">Belt Tests</x-slot>
    <x-admin.page-header title="Belt Tests" description="Create grading events, assign students, and track applications.">
        <x-slot:actions>
            <x-admin.button :href="route('admin.belt-tests.create')">Create belt test</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" />
        <x-admin.input label="Status" name="status" type="select">
            <option value="">All</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-admin.input>
    </x-admin.filters>

    <x-admin.card :padding="false">
        @if ($tests->isEmpty())
            <div class="p-6"><x-admin.empty-state title="No belt tests" description="Create your first grading event." /></div>
        @else
            <x-admin.table>
                <x-slot:head>
                    <th class="px-4 py-3">Test</th>
                    <th class="px-4 py-3">Target belt</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Assigned</th>
                    <th class="px-4 py-3">Apps</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </x-slot:head>
                @foreach ($tests as $test)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $test->title }}</td>
                        <td class="px-4 py-3">{{ $test->targetBelt?->name }}</td>
                        <td class="px-4 py-3">{{ $test->test_date->format('d M Y') }} {{ substr((string) $test->start_time, 0, 5) }}</td>
                        <td class="px-4 py-3">{{ $test->assigned_count }}</td>
                        <td class="px-4 py-3">{{ $test->applications_count }}</td>
                        <td class="px-4 py-3"><x-admin.badge :status="$test->status" /></td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <x-admin.button :href="route('admin.belt-tests.show', $test)" variant="secondary">View</x-admin.button>
                            <x-admin.button :href="route('admin.belt-tests.edit', $test)" variant="ghost">Edit</x-admin.button>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
            <x-admin.pagination :paginator="$tests" />
        @endif
    </x-admin.card>
</x-admin-layout>
