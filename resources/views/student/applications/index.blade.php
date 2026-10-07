<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Applications</h2>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
            @forelse ($applications as $application)
                <a href="{{ route('student.applications.show', $application) }}" class="block bg-white p-5 shadow-sm hover:bg-slate-50 sm:rounded-lg">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ $application->beltTest->title }}</h3>
                            <p class="text-sm text-slate-600">Target: {{ $application->targetBelt?->name }}</p>
                        </div>
                        <x-admin.badge :status="$application->status" />
                    </div>
                </a>
            @empty
                <div class="bg-white p-8 text-sm text-slate-500 shadow-sm sm:rounded-lg">No applications yet.</div>
            @endforelse
            <div>{{ $applications->links() }}</div>
        </div>
    </div>
</x-app-layout>
