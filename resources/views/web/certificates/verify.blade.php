@extends('layouts.public')

@section('title', 'Verify Certificate — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="font-display text-sm uppercase tracking-[0.25em] text-crimson">Certificates</p>
        <h1 class="font-display mt-3 text-5xl uppercase">Verify authenticity</h1>
        <p class="mt-4 max-w-2xl text-white/75">Enter the certificate number and verification code printed on the document.</p>
    </div>
</section>

<section class="bg-paper py-16">
    <div class="site-container max-w-2xl">
        <form method="POST" action="{{ route('certificates.verify.lookup') }}" class="space-y-4 bg-white p-6 sm:p-8">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium">Certificate number</label>
                <input name="certificate_number" value="{{ old('certificate_number', $certificateNumber) }}" required class="w-full border-line font-mono focus:border-crimson focus:ring-crimson">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Verification code</label>
                <input name="verification_code" value="{{ old('verification_code', request('code')) }}" required class="w-full border-line font-mono focus:border-crimson focus:ring-crimson">
            </div>
            @if ($errors->any())
                <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button type="submit" class="bg-crimson px-5 py-3 text-sm font-semibold uppercase tracking-wide text-white hover:bg-crimson-dark">Verify</button>
        </form>

        @if ($searched)
            @if ($certificate && $certificate->isIssued())
                <div class="mt-8 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Certificate verified as authentic and currently valid.
                </div>
                <div class="mt-6">
                    @include('certificates.partials.print', ['certificate' => $certificate])
                </div>
            @elseif ($certificate && ! $certificate->isIssued())
                <div class="mt-8 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    This certificate number matches a <strong>revoked</strong> record and is not valid.
                    @if ($certificate->revoke_reason)
                        <div class="mt-2">Reason: {{ $certificate->revoke_reason }}</div>
                    @endif
                </div>
            @else
                <div class="mt-8 border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    No matching certificate found. Check the number and verification code.
                </div>
            @endif
        @elseif ($certificateNumber && ! ($codeProvided ?? false))
            <div class="mt-8 border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Enter the verification code to confirm authenticity for <span class="font-mono">{{ $certificateNumber }}</span>.
            </div>
        @endif
    </div>
</section>
@endsection
