<x-admin-layout title="Result">
    <x-slot name="header">Result</x-slot>
    <x-admin.page-header
        :title="$result->student->user->name"
        :description="$result->beltTest?->title"
    />

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Outcome</dt><dd><x-admin.badge :status="$result->outcome" /></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Score</dt><dd>{{ $result->score ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Result date</dt><dd>{{ optional($result->result_date)->format('d M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Belt updated</dt><dd>{{ $result->belt_updated ? 'Yes' : 'No' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Target belt</dt><dd>{{ $result->application->targetBelt?->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Recorded by</dt><dd>{{ $result->recorder?->name }}</dd></div>
            </dl>

            @if ($result->admin_notes)
                <p class="mt-4 rounded bg-slate-50 p-3 text-sm text-slate-600">{{ $result->admin_notes }}</p>
            @endif

            <div class="mt-6 border-t border-slate-200 pt-4">
                <h3 class="text-sm font-semibold">Certificate</h3>
                @if ($result->certificate)
                    <div class="mt-2 flex items-center justify-between gap-2 text-sm">
                        <div>
                            <div class="font-mono text-xs">{{ $result->certificate->certificate_number }}</div>
                            <x-admin.badge :status="$result->certificate->status" class="mt-1" />
                        </div>
                        <x-admin.button :href="route('admin.certificates.show', $result->certificate)" variant="secondary">Open</x-admin.button>
                    </div>
                @elseif ($result->isPassed())
                    <form method="POST" action="{{ route('admin.certificates.issue', $result) }}" class="mt-3 space-y-3">
                        @csrf
                        <x-admin.input label="Authorized by" name="authorized_by_name" :value="auth()->user()->name" />
                        <x-admin.button type="submit">Issue certificate</x-admin.button>
                    </form>
                @else
                    <p class="mt-2 text-sm text-slate-500">Certificates are only issued for PASS results.</p>
                @endif
            </div>
        </x-admin.card>

        <x-admin.card class="lg:col-span-2">
            <h3 class="text-sm font-semibold">Correct result</h3>
            <p class="mt-1 text-xs text-slate-500">Corrections are audited. PASS→FAIL reverts belt promotion and revokes the certificate when applicable.</p>

            <form method="POST" action="{{ route('admin.belt-test-results.update', $result) }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <x-admin.input label="Outcome" name="outcome" type="select" required>
                    @foreach ($outcomes as $outcome)
                        <option value="{{ $outcome->value }}" @selected(old('outcome', $result->outcome->value) === $outcome->value)>{{ $outcome->label() }}</option>
                    @endforeach
                </x-admin.input>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-admin.input label="Score" name="score" type="number" :value="old('score', $result->score)" step="0.01" min="0" />
                    <x-admin.input label="Result date" name="result_date" type="date" :value="old('result_date', optional($result->result_date)->format('Y-m-d'))" />
                </div>

                <x-admin.input label="Admin notes" name="admin_notes" type="textarea" :value="old('admin_notes', $result->admin_notes)" />

                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="issue_certificate" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500" @checked(old('issue_certificate', true))>
                    Issue certificate if correcting to PASS
                </label>

                <x-admin.button type="submit">Save correction</x-admin.button>
            </form>
        </x-admin.card>
    </div>
</x-admin-layout>
