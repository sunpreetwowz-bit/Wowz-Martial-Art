<div class="rounded-xl border-2 border-slate-900 bg-white p-8 print:border-black" id="certificate-print">
    <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ config('academy.name') }}</p>
            <h2 class="mt-2 text-3xl font-bold text-slate-900">Certificate of Achievement</h2>
        </div>
        <x-admin.badge :status="$certificate->status" />
    </div>

    <div class="mt-8 space-y-6 text-center">
        <p class="text-sm text-slate-500">This certifies that</p>
        <p class="text-3xl font-semibold text-slate-900">{{ $certificate->student_name_snapshot }}</p>
        <p class="text-sm text-slate-500">
            Student ID <span class="font-mono text-slate-800">{{ $certificate->student_code_snapshot }}</span>
            has successfully graded to
        </p>
        <p class="text-2xl font-bold text-red-700">{{ $certificate->belt_name_snapshot }}</p>

        <div class="mx-auto grid max-w-lg gap-4 pt-4 text-left text-sm sm:grid-cols-2">
            <div>
                <div class="text-slate-500">Certificate number</div>
                <div class="font-mono font-medium">{{ $certificate->certificate_number }}</div>
            </div>
            <div>
                <div class="text-slate-500">Issued on</div>
                <div class="font-medium">{{ optional($certificate->issued_on)->format('d M Y') }}</div>
            </div>
            <div>
                <div class="text-slate-500">Test date</div>
                <div class="font-medium">{{ optional($certificate->test_date)->format('d M Y') ?: '—' }}</div>
            </div>
            <div>
                <div class="text-slate-500">Authorized by</div>
                <div class="font-medium">{{ $certificate->authorized_by_name ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="mt-10 flex items-center justify-between gap-4 border-t border-slate-200 pt-6 text-xs text-slate-500">
        <div>
            Verify at
            <span class="font-mono text-slate-700">{{ url('/verify/certificate/'.$certificate->certificate_number) }}</span>
        </div>
        <button type="button" onclick="window.print()" class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white print:hidden">
            Print
        </button>
    </div>
</div>
