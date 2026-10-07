<x-admin-layout title="Record result">
    <x-slot name="header">Record result</x-slot>
    <x-admin.page-header title="Record belt test result" description="PASS promotes the student and can issue a certificate. FAIL does not change belt rank." />

    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.belt-test-results.store') }}" class="space-y-4">
            @csrf

            <x-admin.input label="Application" name="belt_test_application_id" type="select" required>
                <option value="">Select application</option>
                @foreach ($eligible as $item)
                    <option value="{{ $item->id }}" @selected((int) old('belt_test_application_id', $application?->id) === $item->id)>
                        {{ $item->student->user->name }} — {{ $item->beltTest->title }} ({{ $item->targetBelt?->name }})
                    </option>
                @endforeach
            </x-admin.input>

            @if ($application?->result)
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    This application already has a result. Open it to correct instead.
                </div>
            @endif

            <x-admin.input label="Outcome" name="outcome" type="select" required>
                @foreach ($outcomes as $outcome)
                    <option value="{{ $outcome->value }}" @selected(old('outcome') === $outcome->value)>{{ $outcome->label() }}</option>
                @endforeach
            </x-admin.input>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.input label="Score (optional)" name="score" type="number" :value="old('score')" step="0.01" min="0" />
                <x-admin.input label="Result date" name="result_date" type="date" :value="old('result_date', now()->toDateString())" />
            </div>

            <x-admin.input label="Admin notes" name="admin_notes" type="textarea" :value="old('admin_notes')" />

            <x-admin.input label="Authorized by (certificate)" name="authorized_by_name" :value="old('authorized_by_name', auth()->user()->name)" />

            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="issue_certificate" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500" @checked(old('issue_certificate', true))>
                Issue certificate on PASS
            </label>

            <div class="flex items-center gap-3 pt-2">
                <x-admin.button type="submit">Save result</x-admin.button>
                <x-admin.button :href="route('admin.belt-test-results.index')" variant="secondary">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin-layout>
