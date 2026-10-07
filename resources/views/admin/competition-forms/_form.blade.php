@php
    $selected = collect(old('student_ids', $selectedStudentIds ?? []))->map(fn ($id) => (string) $id);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Title" name="title" :value="old('title', $form?->title)" required />
    <x-admin.input label="Slug (optional)" name="slug" :value="old('slug', $form?->slug)" />
    <x-admin.input label="Deadline" name="deadline_at" type="datetime-local" :value="old('deadline_at', optional($form?->deadline_at)->format('Y-m-d\TH:i'))" />
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $form?->status?->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </x-admin.input>
</div>

<x-admin.input label="Description" name="description" type="textarea" :value="old('description', $form?->description)" />

<div>
    <x-admin.input
        label="Google Form URL (optional)"
        name="google_form_url"
        type="url"
        :value="old('google_form_url', $form?->google_form_url)"
        placeholder="https://docs.google.com/forms/..."
    />
    <p class="mt-1 text-xs text-slate-500">Students can open this link and mark their response as submitted.</p>
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Competition PDF {{ $form ? '(leave empty to keep current)' : '' }}</label>
    <input
        type="file"
        name="pdf"
        accept="application/pdf,.pdf"
        @if (! $form) required @endif
        class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
    >
    @error('pdf')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @if ($form)
        <p class="mt-1 text-xs text-slate-500">Current file is stored privately and served only through authorized downloads.</p>
    @endif
</div>

<div>
    <label class="mb-2 block text-sm font-medium text-slate-700">Assign students</label>
    <div class="max-h-64 overflow-y-auto rounded-lg border border-slate-200 p-3">
        @forelse ($students as $student)
            <label class="flex items-center gap-2 py-1 text-sm">
                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" @checked($selected->contains((string) $student->id)) class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                <span>{{ $student->user->name }} <span class="text-slate-400">({{ $student->student_code }})</span></span>
            </label>
        @empty
            <p class="text-sm text-slate-500">No active students available.</p>
        @endforelse
    </div>
</div>
