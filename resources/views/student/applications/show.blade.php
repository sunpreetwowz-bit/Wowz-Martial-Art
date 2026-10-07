<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Application details</h2>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold">{{ $application->beltTest->title }}</h3>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Status</dt><dd><x-admin.badge :status="$application->status" /></dd></div>
                    <div><dt class="text-slate-500">Submitted</dt><dd>{{ optional($application->submitted_at)->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-slate-500">Current belt</dt><dd>{{ $application->currentBelt?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Target belt</dt><dd>{{ $application->targetBelt?->name }}</dd></div>
                </dl>
                <p class="mt-4 text-xs text-slate-500">Official records cannot be modified by students.</p>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h4 class="font-semibold">Payment</h4>
                @forelse ($application->payments as $payment)
                    <div class="mt-3 border-t border-slate-100 pt-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span>{{ $payment->method->label() }} · {{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                            <div class="flex items-center gap-2">
                                <x-admin.badge :status="$payment->status" />
                                @if (! $payment->method->isOffline() && $payment->status->value === 'pending')
                                    <a href="{{ route('student.payments.checkout', $payment) }}" class="font-medium text-red-700 hover:underline">
                                        Open checkout
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="mt-2 text-sm text-slate-500">No payment required / recorded.</p>
                @endforelse
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h4 class="font-semibold">Result</h4>
                @if ($application->result)
                    <div class="mt-3 flex items-center justify-between text-sm">
                        <div>
                            <x-admin.badge :status="$application->result->outcome" />
                            <span class="ml-2 text-slate-600">{{ optional($application->result->result_date)->format('d M Y') }}</span>
                        </div>
                        @if ($application->result->certificate)
                            <a href="{{ route('student.certificates.show', $application->result->certificate) }}" class="font-medium text-red-700 hover:underline">
                                View certificate
                            </a>
                        @endif
                    </div>
                @else
                    <p class="mt-2 text-sm text-slate-500">Result not published yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
