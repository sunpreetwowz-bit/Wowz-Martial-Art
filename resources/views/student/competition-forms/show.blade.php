<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $form->title }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            @if ($form->isDeadlinePassed())
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    The deadline for this form has passed ({{ $form->deadline_at->format('d M Y H:i') }}). You can still download the PDF if needed.
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">Deadline</dt>
                        <dd class="font-medium {{ $form->isDeadlinePassed() ? 'text-red-700' : '' }}">
                            {{ optional($form->deadline_at)->format('d M Y H:i') ?: 'No deadline' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Assignment status</dt>
                        <dd><x-admin.badge :status="$assignment->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Viewed</dt>
                        <dd>{{ optional($assignment->viewed_at)->format('d M Y H:i') ?: 'Just now' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Downloaded</dt>
                        <dd>{{ optional($assignment->downloaded_at)->format('d M Y H:i') ?: 'Not yet' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Response</dt>
                        <dd><x-admin.badge :status="$assignment->response_status ?? 'pending'" /></dd>
                    </div>
                </dl>

                @if ($form->description)
                    <div class="mt-6 border-t border-slate-100 pt-4 text-sm text-slate-700 whitespace-pre-line">{{ $form->description }}</div>
                @endif

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('student.competition-forms.download', $form) }}" class="inline-flex rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        Download PDF
                    </a>

                    @if ($form->google_form_url)
                        <a href="{{ $form->google_form_url }}" target="_blank" rel="noopener" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Open Google Form
                        </a>
                    @endif
                </div>

                <p class="mt-2 text-xs text-slate-500">You can view and download this form, but you cannot change the original PDF.</p>

                @if ($form->google_form_url && ($assignment->response_status?->value ?? 'pending') !== 'responded')
                    <form method="POST" action="{{ route('student.competition-forms.responded', $form) }}" class="mt-6 border-t border-slate-100 pt-6">
                        @csrf
                        <p class="mb-3 text-sm text-slate-600">After you submit the Google Form, mark your response here so the academy can track it.</p>
                        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            I've submitted the Google Form
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
