<x-admin-layout title="Results">
    <x-slot name="header">Results</x-slot>
    <x-admin.page-header title="Belt test results" description="Record PASS/FAIL outcomes, promote belts, and manage certificates.">
        <x-slot name="actions">
            <x-admin.button :href="route('admin.belt-test-results.create')">Record result</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" placeholder="Student or test title" />
        <x-admin.input label="Outcome" name="outcome" type="select">
            <option value="">All</option>
            @foreach ($outcomes as $outcome)
                <option value="{{ $outcome->value }}" @selected(request('outcome') === $outcome->value)>{{ $outcome->label() }}</option>
            @endforeach
        </x-admin.input>
    </x-admin.filters>

    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Student</th>
                <th class="px-4 py-3">Test</th>
                <th class="px-4 py-3">Outcome</th>
                <th class="px-4 py-3">Result date</th>
                <th class="px-4 py-3">Belt updated</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @forelse ($results as $result)
                <tr>
                    <td class="px-4 py-3">{{ $result->student->user->name }}</td>
                    <td class="px-4 py-3">{{ $result->beltTest?->title }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$result->outcome" /></td>
                    <td class="px-4 py-3">{{ optional($result->result_date)->format('d M Y') }}</td>
                    <td class="px-4 py-3">{{ $result->belt_updated ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-right">
                        <x-admin.button :href="route('admin.belt-test-results.show', $result)" variant="secondary">View</x-admin.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No results recorded yet.</td>
                </tr>
            @endforelse
        </x-admin.table>
        <x-admin.pagination :paginator="$results" />
    </x-admin.card>
</x-admin-layout>
