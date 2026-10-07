<x-admin-layout title="Application">
    <x-slot name="header">Application</x-slot>
    <x-admin.page-header :title="$application->student->user->name" :description="$application->beltTest->title" />

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-admin.badge :status="$application->status" /></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Current belt</dt><dd>{{ $application->currentBelt?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Target belt</dt><dd>{{ $application->targetBelt?->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Submitted</dt><dd>{{ optional($application->submitted_at)->format('d M Y H:i') }}</dd></div>
            </dl>
            @if ($application->form_data)
                <pre class="mt-4 overflow-x-auto rounded bg-slate-50 p-3 text-xs">{{ json_encode($application->form_data, JSON_PRETTY_PRINT) }}</pre>
            @endif

            @if (! $application->result)
                <div class="mt-6">
                    <x-admin.button :href="route('admin.belt-test-results.create', ['application' => $application->id])">
                        Record result
                    </x-admin.button>
                </div>
            @else
                <div class="mt-6">
                    <x-admin.button :href="route('admin.belt-test-results.show', $application->result)" variant="secondary">
                        View result
                    </x-admin.button>
                </div>
            @endif
        </x-admin.card>

        <x-admin.card class="lg:col-span-2">
            <h3 class="text-sm font-semibold">Payments</h3>
            <div class="mt-3 space-y-3">
                @forelse ($application->payments as $payment)
                    <div class="flex items-center justify-between rounded border border-slate-200 p-3 text-sm">
                        <div>
                            <div class="font-medium">{{ $payment->method->label() }} · {{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
                            <div class="text-xs text-slate-500">{{ $payment->uuid }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-admin.badge :status="$payment->status" />
                            <x-admin.button :href="route('admin.payments.show', $payment)" variant="secondary">Open</x-admin.button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No payment records.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.belt-test-applications.review', $application) }}" class="mt-6 space-y-3 border-t border-slate-200 pt-6">
                @csrf
                @if (session('error'))
                    <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ session('error') }}</div>
                @endif
                @if (session('success'))
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
                @endif
                <x-admin.input label="Update status" name="status" type="select" required>
                    @foreach ($reviewStatuses as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $application->status?->value) === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </x-admin.input>
                <p class="text-xs text-slate-500">
                    Passed / Failed are set when you record a result. Payment Pending is set when the student applies.
                </p>
                <x-admin.input label="Admin notes" name="admin_notes" type="textarea" :value="old('admin_notes', $application->admin_notes)" />
                <x-admin.button type="submit">Save review</x-admin.button>
            </form>
        </x-admin.card>
    </div>
</x-admin-layout>
