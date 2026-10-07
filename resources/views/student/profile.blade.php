<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="grid gap-6 p-6 md:grid-cols-3">
                    <div class="text-center md:text-left">
                        @if ($student->profile_photo_path)
                            <img src="{{ asset('storage/'.$student->profile_photo_path) }}" alt="" class="mx-auto h-24 w-24 rounded-full object-cover md:mx-0">
                        @else
                            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-slate-100 text-2xl font-semibold text-slate-500 md:mx-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="md:col-span-2">
                        <h3 class="text-xl font-semibold text-slate-900">{{ auth()->user()->name }}</h3>
                        <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
                        <dl class="mt-4 grid gap-3 sm:grid-cols-2 text-sm">
                            <div><dt class="text-slate-500">Student ID</dt><dd class="font-mono font-medium">{{ $student->student_code }}</dd></div>
                            <div><dt class="text-slate-500">Current belt</dt><dd class="font-medium">{{ $student->currentBelt?->name ?? 'Not assigned' }}</dd></div>
                            <div><dt class="text-slate-500">Service</dt><dd class="font-medium">{{ $student->primaryService?->name ?? '—' }}</dd></div>
                            <div><dt class="text-slate-500">Phone</dt><dd class="font-medium">{{ $student->phone ?: '—' }}</dd></div>
                            <div><dt class="text-slate-500">Joined</dt><dd class="font-medium">{{ optional($student->joining_date)->format('d M Y') ?? '—' }}</dd></div>
                            <div><dt class="text-slate-500">Status</dt><dd><x-admin.badge :status="$student->status" /></dd></div>
                        </dl>
                        <p class="mt-4 text-xs text-slate-500">Official records (belt, results, certificates) can only be updated by the academy admin.</p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-slate-100 px-6 py-4 font-semibold">Belt history</div>
                <div class="divide-y divide-slate-100">
                    @forelse ($student->beltHistories as $history)
                        <div class="flex items-center justify-between px-6 py-4 text-sm">
                            <div>
                                <p class="font-medium">{{ $history->fromBelt?->name ?? 'None' }} → {{ $history->toBelt?->name }}</p>
                                <p class="text-xs text-slate-500">{{ $history->source->label() }}</p>
                            </div>
                            <p class="text-xs text-slate-500">{{ $history->created_at->format('d M Y') }}</p>
                        </div>
                    @empty
                        <p class="px-6 py-8 text-sm text-slate-500">No belt history available.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <a href="{{ route('student.dashboard') }}" class="text-sm font-medium text-red-700 hover:underline">← Back to dashboard</a>
            </div>
        </div>
    </div>
</x-app-layout>
