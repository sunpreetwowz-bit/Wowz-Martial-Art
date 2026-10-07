<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="grid gap-6 p-6 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <p class="text-sm text-slate-500">Welcome back</p>
                        <h3 class="mt-1 text-2xl font-semibold text-slate-900">{{ auth()->user()->name }}</h3>
                        <p class="mt-2 text-sm text-slate-600">
                            Student ID: <span class="font-mono font-semibold">{{ $student->student_code }}</span>
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Status: <x-admin.badge :status="$student->status" class="align-middle" />
                        </p>
                    </div>
                    <div class="rounded-xl bg-slate-950 p-5 text-white">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Current belt</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $student->currentBelt?->name ?? 'Not assigned' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <h3 class="font-semibold text-slate-900">Upcoming belt tests</h3>
                        <a href="{{ route('student.belt-tests.index') }}" class="text-sm font-medium text-red-700 hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($upcomingTests as $test)
                            <div class="px-6 py-4 text-sm">
                                <div class="font-medium text-slate-900">{{ $test->title }}</div>
                                <div class="text-slate-600">{{ $test->test_date->format('d M Y') }} · closes {{ $test->closes_at->format('d M H:i') }}</div>
                                <div class="mt-2">
                                    @if ($test->can_apply)
                                        <a href="{{ route('student.belt-tests.apply', $test) }}" class="font-semibold text-red-700 hover:underline">Apply now</a>
                                    @else
                                        <a href="{{ route('student.belt-tests.show', $test) }}" class="text-slate-600 hover:underline">View details</a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="px-6 py-8 text-sm text-slate-500">No upcoming assigned tests.</p>
                        @endforelse
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <h3 class="font-semibold text-slate-900">Recent applications</h3>
                        <a href="{{ route('student.applications.index') }}" class="text-sm font-medium text-red-700 hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($student->applications as $application)
                            <a href="{{ route('student.applications.show', $application) }}" class="flex items-center justify-between px-6 py-4 text-sm hover:bg-slate-50">
                                <div>
                                    <div class="font-medium">{{ $application->beltTest->title }}</div>
                                    <div class="text-xs text-slate-500">{{ optional($application->submitted_at)->format('d M Y') }}</div>
                                </div>
                                <x-admin.badge :status="$application->status" />
                            </a>
                        @empty
                            <p class="px-6 py-8 text-sm text-slate-500">No applications yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="font-semibold text-slate-900">Competition forms</h3>
                    <a href="{{ route('student.competition-forms.index') }}" class="text-sm font-medium text-red-700 hover:underline">View all</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($competitionForms as $form)
                        <a href="{{ route('student.competition-forms.show', $form) }}" class="block px-6 py-4 text-sm hover:bg-slate-50">
                            <div class="font-medium text-slate-900">{{ $form->title }}</div>
                            <div @class(['text-slate-600', 'text-red-700' => $form->isDeadlinePassed()])>
                                @if ($form->deadline_at)
                                    Deadline {{ $form->deadline_at->format('d M Y H:i') }}
                                    @if ($form->isDeadlinePassed()) · passed @endif
                                @else
                                    No deadline
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="px-6 py-8 text-sm text-slate-500">No competition forms assigned.</p>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-slate-100 px-6 py-4 font-semibold text-slate-900">Belt history</div>
                <div class="p-6">
                    @forelse ($student->beltHistories as $history)
                        <div class="flex items-start justify-between gap-3 border-b border-slate-100 py-3 last:border-0">
                            <div>
                                <p class="font-medium text-slate-900">
                                    {{ $history->fromBelt?->name ?? 'None' }} → {{ $history->toBelt?->name }}
                                </p>
                                <p class="text-xs text-slate-500">{{ $history->source->label() }}</p>
                            </div>
                            <p class="text-xs text-slate-500">{{ $history->created_at->format('d M Y') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No belt history yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
