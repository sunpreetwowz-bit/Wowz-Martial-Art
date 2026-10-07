<x-admin-layout :title="$test->title">
    <x-slot name="header">Belt Test</x-slot>
    <x-admin.page-header :title="$test->title" :description="$test->targetBelt?->name">
        <x-slot:actions>
            <x-admin.button :href="route('admin.belt-tests.edit', $test)" variant="primary">Edit</x-admin.button>
            <x-admin.button :href="route('admin.belt-tests.index')" variant="secondary">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Date</dt><dd>{{ $test->test_date->format('d M Y') }} {{ substr((string)$test->start_time,0,5) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Fee</dt><dd>{{ $test->currency }} {{ number_format($test->fee_amount, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-admin.badge :status="$test->status" /></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Closes at</dt><dd class="font-medium text-red-700">{{ $closesAt->format('d M Y H:i') }}</dd></div>
            </dl>
            @if ($test->instructions)
                <p class="mt-4 text-sm text-slate-600 whitespace-pre-line">{{ $test->instructions }}</p>
            @endif
        </x-admin.card>

        <x-admin.card class="lg:col-span-2" :padding="false">
            <x-slot:header><h3 class="text-sm font-semibold">Assigned students</h3></x-slot:header>
            <x-admin.table>
                <x-slot:head>
                    <th class="px-4 py-3">Student</th>
                    <th class="px-4 py-3">Status</th>
                </x-slot:head>
                @foreach ($test->assignments as $assignment)
                    <tr>
                        <td class="px-4 py-3">{{ $assignment->student->user->name }} <span class="text-xs text-slate-400">{{ $assignment->student->student_code }}</span></td>
                        <td class="px-4 py-3"><x-admin.badge :status="$assignment->status" /></td>
                    </tr>
                @endforeach
            </x-admin.table>
        </x-admin.card>
    </div>

    <x-admin.card class="mt-6" :padding="false">
        <x-slot:header><h3 class="text-sm font-semibold">Applications</h3></x-slot:header>
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Student</th>
                <th class="px-4 py-3">Submitted</th>
                <th class="px-4 py-3">Payment</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @forelse ($test->applications as $application)
                <tr>
                    <td class="px-4 py-3">{{ $application->student->user->name }}</td>
                    <td class="px-4 py-3">{{ optional($application->submitted_at)->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">
                        @if ($application->latestPayment)
                            <x-admin.badge :status="$application->latestPayment->status" />
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3"><x-admin.badge :status="$application->status" /></td>
                    <td class="px-4 py-3 text-right">
                        <x-admin.button :href="route('admin.belt-test-applications.show', $application)" variant="secondary">Open</x-admin.button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-sm text-slate-500">No applications yet.</td></tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>
</x-admin-layout>
