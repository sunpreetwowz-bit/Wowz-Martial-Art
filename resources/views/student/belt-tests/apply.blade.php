<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Apply — {{ $test->title }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl space-y-6 sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
                Application closes at <strong>{{ $closesAt->format('d M Y H:i') }}</strong>.
                After submission you cannot edit this application.
            </div>

            <form method="POST" action="{{ route('student.belt-tests.apply.submit', $test) }}" class="space-y-4 bg-white p-6 shadow-sm sm:rounded-lg">
                @csrf
                <div class="text-sm text-slate-600">
                    <p>Student: <strong>{{ $student->user->name }}</strong> ({{ $student->student_code }})</p>
                    <p>Current belt: <strong>{{ $student->currentBelt?->name ?? 'Not assigned' }}</strong></p>
                    <p>Target belt: <strong>{{ $test->targetBelt?->name }}</strong></p>
                    <p>Fee: <strong>{{ $test->currency }} {{ number_format($test->fee_amount, 2) }}</strong></p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Payment method</label>
                    <select name="payment_method" required class="w-full rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>{{ $method->label() }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Cash stays Payment Pending until admin verifies. Online methods are verified server-side.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Emergency contact (optional)</label>
                    <input name="emergency_contact" value="{{ old('emergency_contact') }}" class="w-full rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Notes (optional)</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">{{ old('notes') }}</textarea>
                </div>

                <label class="flex items-start gap-2 text-sm">
                    <input type="checkbox" name="acknowledge" value="1" required class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500" @checked(old('acknowledge'))>
                    <span>I confirm my details are correct and understand this application cannot be edited after submission.</span>
                </label>

                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Submit application</button>
            </form>
        </div>
    </div>
</x-app-layout>
