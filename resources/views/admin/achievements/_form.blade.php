<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Title" name="title" :value="old('title', $achievement?->title)" required />
    <x-admin.input label="Student" name="student_id" type="select">
        <option value="">None</option>
        @foreach ($students as $student)
            <option value="{{ $student->id }}" @selected((string) old('student_id', $achievement?->student_id) === (string) $student->id)>
                {{ $student->user->name }} ({{ $student->student_code }})
            </option>
        @endforeach
    </x-admin.input>
    <x-admin.input label="Competition / Event" name="competition_name" :value="old('competition_name', $achievement?->competition_name)" />
    <x-admin.input label="Type" name="achievement_type" :value="old('achievement_type', $achievement?->achievement_type)" />
    <x-admin.input label="Position" name="position" :value="old('position', $achievement?->position)" />
    <x-admin.input label="Date" name="achieved_on" type="date" :value="old('achieved_on', optional($achievement?->achieved_on)->format('Y-m-d'))" />
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $achievement?->status?->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </x-admin.input>
</div>
<x-admin.input label="Description" name="description" type="textarea" :value="old('description', $achievement?->description)" />
<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Image" name="image" type="file" accept="image/*" />
    <x-admin.input label="Document" name="document" type="file" />
</div>
