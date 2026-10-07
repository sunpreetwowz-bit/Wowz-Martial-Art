<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Certificates</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
            @forelse ($certificates as $certificate)
                <a href="{{ route('student.certificates.show', $certificate) }}" class="block bg-white p-5 shadow-sm hover:bg-slate-50 sm:rounded-lg">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ $certificate->belt_name_snapshot }}</h3>
                            <p class="mt-1 font-mono text-xs text-slate-500">{{ $certificate->certificate_number }}</p>
                            <p class="mt-1 text-sm text-slate-600">Issued {{ optional($certificate->issued_on)->format('d M Y') }}</p>
                        </div>
                        <x-admin.badge :status="$certificate->status" />
                    </div>
                </a>
            @empty
                <div class="bg-white p-8 text-sm text-slate-500 shadow-sm sm:rounded-lg">No certificates yet.</div>
            @endforelse
            <div>{{ $certificates->links() }}</div>
        </div>
    </div>
</x-app-layout>
