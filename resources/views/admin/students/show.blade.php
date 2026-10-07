<x-admin-layout :title="$student->user->name">
    <x-slot name="header">Student Profile</x-slot>

    <x-admin.page-header :title="$student->user->name" :description="$student->student_code">
        <x-slot:actions>
            <x-admin.button :href="route('admin.students.edit', $student)" variant="primary">Edit</x-admin.button>
            <x-admin.button :href="route('admin.students.index')" variant="secondary">Back to list</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if (session('temporary_password'))
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <p class="font-semibold">Temporary password (copy now — it will not be shown again):</p>
            <p class="mt-1 font-mono text-base">{{ session('temporary_password') }}</p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card class="lg:col-span-1">
            <div class="text-center">
                @if ($student->profile_photo_path)
                    <img src="{{ asset('storage/'.$student->profile_photo_path) }}" alt="" class="mx-auto h-28 w-28 rounded-full object-cover ring-2 ring-slate-200">
                @else
                    <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-slate-100 text-2xl font-semibold text-slate-500">
                        {{ strtoupper(substr($student->user->name, 0, 1)) }}
                    </div>
                @endif
                <h2 class="mt-4 text-lg font-semibold text-slate-900">{{ $student->user->name }}</h2>
                <p class="text-sm text-slate-500">{{ $student->user->email }}</p>
                <div class="mt-3"><x-admin.badge :status="$student->status" /></div>
            </div>

            <dl class="mt-6 space-y-3 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Student ID</dt><dd class="font-mono">{{ $student->student_code }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Current belt</dt><dd class="font-medium">{{ $student->currentBelt?->name ?? 'Not assigned' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Service</dt><dd>{{ $student->primaryService?->name ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Joined</dt><dd>{{ optional($student->joining_date)->format('d M Y') ?? '—' }}</dd></div>
            </dl>

            <div class="mt-6 flex flex-col gap-2">
                @if ($student->status === \App\Enums\AccountStatus::Active)
                    <form method="POST" action="{{ route('admin.students.deactivate', $student) }}">
                        @csrf
                        <x-admin.button type="submit" variant="danger" class="w-full">Deactivate</x-admin.button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.students.activate', $student) }}">
                        @csrf
                        <x-admin.button type="submit" variant="primary" class="w-full">Activate</x-admin.button>
                    </form>
                @endif
            </div>
        </x-admin.card>

        <div class="space-y-6 lg:col-span-2">
            <x-admin.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Contact & personal details</h3></x-slot:header>
                <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div><dt class="text-slate-500">Phone</dt><dd class="font-medium">{{ $student->phone ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Gender</dt><dd class="font-medium">{{ $student->gender?->label() ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Date of birth</dt><dd class="font-medium">{{ optional($student->date_of_birth)->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Emergency contact</dt><dd class="font-medium">{{ $student->emergency_contact_name ?: '—' }} {{ $student->emergency_contact_phone ? '('.$student->emergency_contact_phone.')' : '' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Address</dt><dd class="font-medium">{{ $student->address ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Notes</dt><dd class="font-medium">{{ $student->notes ?: '—' }}</dd></div>
                </dl>
            </x-admin.card>

            <x-admin.card :padding="false">
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Belt history</h3></x-slot:header>
                @if ($student->beltHistories->isEmpty())
                    <div class="p-6 text-sm text-slate-500">No belt history recorded yet.</div>
                @else
                    <x-admin.table>
                        <x-slot:head>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">From</th>
                            <th class="px-4 py-3">To</th>
                            <th class="px-4 py-3">Source</th>
                            <th class="px-4 py-3">By</th>
                        </x-slot:head>
                        @foreach ($student->beltHistories->sortByDesc('created_at') as $history)
                            <tr>
                                <td class="px-4 py-3">{{ $history->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $history->fromBelt?->name ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $history->toBelt?->name }}</td>
                                <td class="px-4 py-3">{{ $history->source->label() }}</td>
                                <td class="px-4 py-3">{{ $history->creator?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </x-admin.table>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
