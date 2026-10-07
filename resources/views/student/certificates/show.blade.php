<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Certificate</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">Certificate number</dt>
                        <dd class="font-mono font-medium">{{ $certificate->certificate_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Status</dt>
                        <dd><x-admin.badge :status="$certificate->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Belt</dt>
                        <dd class="font-medium">{{ $certificate->belt_name_snapshot }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Issued on</dt>
                        <dd>{{ optional($certificate->issued_on)->format('d M Y') }}</dd>
                    </div>
                    @if ($certificate->isIssued())
                        <div class="sm:col-span-2">
                            <dt class="text-slate-500">Public verification</dt>
                            <dd class="mt-1 text-sm">
                                Share
                                <a href="{{ route('certificates.verify', ['certificateNumber' => $certificate->certificate_number, 'code' => $certificate->verification_code]) }}" class="font-medium text-red-700 hover:underline" target="_blank" rel="noopener">
                                    this verification link
                                </a>
                                or code <span class="font-mono">{{ $certificate->verification_code }}</span>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <a href="{{ route('student.certificates.download', $certificate) }}" class="inline-flex rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                Download PDF
                            </a>
                        </div>
                    @endif
                </dl>
            </div>

            @include('certificates.partials.print', ['certificate' => $certificate])
        </div>
    </div>
</x-app-layout>
