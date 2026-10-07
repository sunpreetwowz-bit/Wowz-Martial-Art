@if (session('success'))
    <div class="mb-4 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div class="mb-4 border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="alert">
        {{ session('warning') }}
    </div>
@endif

@if (session('info'))
    <div class="mb-4 border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800" role="alert">
        {{ session('info') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <p class="font-semibold">Please fix the following errors:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
