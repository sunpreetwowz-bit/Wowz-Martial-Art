<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $test->title }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Target belt</dt><dd class="font-medium">{{ $test->targetBelt?->name }}</dd></div>
                    <div><dt class="text-slate-500">Test starts</dt><dd class="font-medium">{{ $test->test_date->format('d M Y') }} {{ substr((string)$test->start_time,0,5) }}</dd></div>
                    <div><dt class="text-slate-500">Application closes</dt><dd class="font-semibold text-red-700">{{ $closesAt->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-slate-500">Fee</dt><dd class="font-medium">{{ $test->currency }} {{ number_format($test->fee_amount, 2) }}</dd></div>
                </dl>
                @if ($test->instructions)
                    <div class="mt-4 whitespace-pre-line text-sm text-slate-600">{{ $test->instructions }}</div>
                @endif

                <div class="mt-6 flex gap-2">
                    @if ($canApply)
                        <a href="{{ route('student.belt-tests.apply', $test) }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white">Apply now</a>
                    @elseif ($application)
                        <a href="{{ route('student.applications.show', $application) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">View application</a>
                    @else
                        <p class="text-sm text-slate-500">Applications are not available for this test right now.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
