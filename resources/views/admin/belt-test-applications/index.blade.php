<x-admin-layout title="Applications">
    <x-slot name="header">Belt Test Applications</x-slot>
    <x-admin.page-header title="Applications" description="Review submissions and payment status." />
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
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Student</th>
                <th class="px-4 py-3">Test</th>
                <th class="px-4 py-3">Target</th>
                <th class="px-4 py-3">Payment</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($applications as $application)
                <tr>
                    <td class="px-4 py-3">{{ $application->student->user->name }}</td>
                    <td class="px-4 py-3">{{ $application->beltTest->title }}</td>
                    <td class="px-4 py-3">{{ $application->targetBelt?->name }}</td>
                    <td class="px-4 py-3">
                        @if ($application->latestPayment)
                            <x-admin.badge :status="$application->latestPayment->status" />
                        @else —
                        @endif
                    </td>
                    <td class="px-4 py-3"><x-admin.badge :status="$application->status" /></td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.belt-test-applications.show', $application)" variant="secondary">View</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$applications" />
    </x-admin.card>
</x-admin-layout>
