<x-admin-layout title="Certificate">
    <x-slot name="header">Certificate</x-slot>
    <x-admin.page-header
        :title="$certificate->certificate_number"
        :description="$certificate->student_name_snapshot.' · '.$certificate->belt_name_snapshot"
    >
        <x-slot name="actions">
            @if ($certificate->isIssued())
                <x-admin.button :href="route('admin.certificates.download', $certificate)" variant="secondary">Download PDF</x-admin.button>
            @endif
        </x-slot>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-admin.badge :status="$certificate->status" /></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Student code</dt><dd class="font-mono">{{ $certificate->student_code_snapshot }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Issued on</dt><dd>{{ optional($certificate->issued_on)->format('d M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Test date</dt><dd>{{ optional($certificate->test_date)->format('d M Y') ?: '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Authorized by</dt><dd>{{ $certificate->authorized_by_name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Issued by</dt><dd>{{ $certificate->issuer?->name ?? '—' }}</dd></div>
            </dl>

            <div class="mt-4 rounded-lg bg-slate-50 p-3 text-xs">
                <div class="text-slate-500">Verification code</div>
                <div class="mt-1 font-mono text-sm text-slate-900">{{ $certificate->verification_code }}</div>
                <a href="{{ route('certificates.verify', ['certificateNumber' => $certificate->certificate_number, 'code' => $certificate->verification_code]) }}" class="mt-2 inline-block text-red-700 hover:underline" target="_blank" rel="noopener">
                    Open public verify link
                </a>
            </div>

            @if ($certificate->isIssued())
                <form method="POST" action="{{ route('admin.certificates.revoke', $certificate) }}" class="mt-6 space-y-3 border-t border-slate-200 pt-6">
                    @csrf
                    <x-admin.input label="Revoke reason" name="revoke_reason" type="textarea" required />
                    <x-admin.button type="submit" variant="danger">Revoke certificate</x-admin.button>
                </form>
            @elseif ($certificate->revoke_reason)
                <p class="mt-6 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    Revoked {{ optional($certificate->revoked_at)->format('d M Y H:i') }}: {{ $certificate->revoke_reason }}
                </p>
            @endif
        </x-admin.card>

        <div class="lg:col-span-2">
            @include('certificates.partials.print', ['certificate' => $certificate])
        </div>
    </div>
</x-admin-layout>
