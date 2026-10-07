<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Belt Tests</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
            @forelse ($tests as $test)
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $test->title }}</h3>
                            <p class="text-sm text-slate-600">
                                Target: {{ $test->targetBelt?->name }} ·
                                {{ $test->test_date->format('d M Y') }} {{ substr((string)$test->start_time,0,5) }}
                            </p>
                            <p class="mt-1 text-sm font-medium text-red-700">
                                Apply before: {{ $test->closes_at->format('d M Y H:i') }}
                            </p>
                            @if ($test->my_application)
                                <p class="mt-2 text-sm">Application: <x-admin.badge :status="$test->my_application->status" /></p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('student.belt-tests.show', $test) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">Details</a>
                            @if ($test->can_apply)
                                <a href="{{ route('student.belt-tests.apply', $test) }}" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white">Apply</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 text-sm text-slate-500 shadow-sm sm:rounded-lg">No belt tests assigned to you yet.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
