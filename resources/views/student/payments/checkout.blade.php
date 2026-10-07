<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sandbox checkout</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                This is a sandbox gateway for local/demo use. No real money is charged.
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold text-slate-900">{{ $payment->application?->beltTest?->title ?? 'Belt test fee' }}</h3>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">Amount</dt>
                        <dd class="font-medium">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Method</dt>
                        <dd class="font-medium">{{ $payment->method->label() }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Reference</dt>
                        <dd class="font-mono text-xs">{{ $payment->uuid }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                    <form method="POST" action="{{ route('student.payments.checkout.complete', $payment) }}">
                        @csrf
                        <input type="hidden" name="outcome" value="success">
                        <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Simulate successful payment
                        </button>
                    </form>
                    <form method="POST" action="{{ route('student.payments.checkout.complete', $payment) }}">
                        @csrf
                        <input type="hidden" name="outcome" value="failed">
                        <button class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Simulate failed payment
                        </button>
                    </form>
                </div>

                <p class="mt-4 text-xs text-slate-500">
                    <a href="{{ route('student.applications.show', $payment->application) }}" class="text-red-700 hover:underline">Skip for now</a>
                    — cash/admin verification remains available for offline methods.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
