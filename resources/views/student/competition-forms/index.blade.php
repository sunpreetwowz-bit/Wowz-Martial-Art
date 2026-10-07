<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Competition Forms</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
            @forelse ($forms as $form)
                @php $assignment = $form->assignments->first(); @endphp
                <a href="{{ route('student.competition-forms.show', $form) }}" class="block bg-white p-5 shadow-sm hover:bg-slate-50 sm:rounded-lg">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ $form->title }}</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                Deadline:
                                @if ($form->deadline_at)
                                    <span @class(['font-medium', 'text-red-700' => $form->isDeadlinePassed()])>
                                        {{ $form->deadline_at->format('d M Y H:i') }}
                                        @if ($form->isDeadlinePassed()) (passed) @endif
                                    </span>
                                @else
                                    No deadline
                                @endif
                            </p>
                        </div>
                        <div class="text-right text-xs text-slate-500">
                            @if ($assignment?->downloaded_at)
                                Downloaded
                            @elseif ($assignment?->viewed_at)
                                Viewed
                            @else
                                New
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white p-8 text-sm text-slate-500 shadow-sm sm:rounded-lg">No competition forms assigned to you.</div>
            @endforelse
            <div>{{ $forms->links() }}</div>
        </div>
    </div>
</x-app-layout>
