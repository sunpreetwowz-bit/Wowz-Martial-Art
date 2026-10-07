<x-admin-layout title="Payment">
    <x-slot name="header">Payment</x-slot>
    <x-admin.page-header title="Payment details" :description="$payment->uuid" />
    <x-admin.card class="max-w-3xl">
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-500">Student</dt><dd class="font-medium">{{ $payment->student->user->name }}</dd></div>
            <div><dt class="text-slate-500">Method</dt><dd>{{ $payment->method->label() }}</dd></div>
            <div><dt class="text-slate-500">Amount</dt><dd>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</dd></div>
            <div><dt class="text-slate-500">Status</dt><dd><x-admin.badge :status="$payment->status" /></dd></div>
            <div><dt class="text-slate-500">Gateway ref</dt><dd class="font-mono text-xs">{{ $payment->gateway_payment_id ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">Verified by</dt><dd>{{ $payment->verifier?->name ?? '—' }}</dd></div>
        </dl>

        @if ($payment->status !== \App\Enums\PaymentStatus::Paid)
            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="mt-6 space-y-3 border-t border-slate-200 pt-6">
                @csrf
                <x-admin.input label="Verification notes" name="notes" type="textarea" />
                <x-admin.button type="submit">Mark as Paid (verify)</x-admin.button>
                <p class="text-xs text-slate-500">Cash/UPI/Paytm remain pending until you verify. Never trust browser-only status.</p>
            </form>
        @endif
    </x-admin.card>
</x-admin-layout>
