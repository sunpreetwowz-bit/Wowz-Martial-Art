<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Title" name="title" :value="old('title', $test?->title)" required />
    <x-admin.input label="Slug (optional)" name="slug" :value="old('slug', $test?->slug)" />
    <x-admin.input label="Target belt" name="target_belt_id" type="select" required>
        <option value="">Select belt</option>
        @foreach ($belts as $belt)
            <option value="{{ $belt->id }}" @selected((string) old('target_belt_id', $test?->target_belt_id) === (string) $belt->id)>{{ $belt->name }}</option>
        @endforeach
    </x-admin.input>
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $test?->status?->value ?? 'scheduled') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </x-admin.input>
    <x-admin.input label="Test date" name="test_date" type="date" :value="old('test_date', optional($test?->test_date)->format('Y-m-d'))" required />
    <x-admin.input label="Start time" name="start_time" type="time" :value="old('start_time', $test?->start_time ? substr($test->start_time,0,5) : null)" required />
    <x-admin.input label="End time" name="end_time" type="time" :value="old('end_time', $test?->end_time ? substr($test->end_time,0,5) : null)" />
    <x-admin.input label="Fee amount" name="fee_amount" type="number" step="0.01" :value="old('fee_amount', $test?->fee_amount ?? 0)" required />
    <x-admin.input label="Application opens at" name="application_opens_at" type="datetime-local" :value="old('application_opens_at', optional($test?->application_opens_at)->format('Y-m-d\TH:i'))" />
    <x-admin.input label="Application closes at (optional)" name="application_closes_at" type="datetime-local" :value="old('application_closes_at', optional($test?->application_closes_at)->format('Y-m-d\TH:i'))" />
</div>
<p class="text-xs text-slate-500">If close time is empty, applications close automatically 30 minutes before test start.</p>
<x-admin.input label="Instructions" name="instructions" type="textarea" :value="old('instructions', $test?->instructions)" />

<div>
    <label class="mb-2 block text-sm font-medium text-slate-700">Assigned students</label>
    <div class="max-h-64 overflow-y-auto rounded-lg border border-slate-200 p-3">
        @php $selected = collect(old('student_ids', $selectedStudentIds ?? []))->map(fn ($id) => (string) $id); @endphp
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
