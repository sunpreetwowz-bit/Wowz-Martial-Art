<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Title" name="title" :value="old('title', $event?->title)" required />
    <x-admin.input label="Slug (optional)" name="slug" :value="old('slug', $event?->slug)" />
    <x-admin.input label="Start date" name="start_date" type="date" :value="old('start_date', optional($event?->start_date)->format('Y-m-d'))" required />
    <x-admin.input label="End date" name="end_date" type="date" :value="old('end_date', optional($event?->end_date)->format('Y-m-d'))" />
    <x-admin.input label="Start time" name="start_time" type="time" :value="old('start_time', $event?->start_time ? substr($event->start_time,0,5) : null)" />
    <x-admin.input label="End time" name="end_time" type="time" :value="old('end_time', $event?->end_time ? substr($event->end_time,0,5) : null)" />
    <x-admin.input label="Venue" name="venue" :value="old('venue', $event?->venue)" />
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $event?->status?->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </x-admin.input>
</div>
<x-admin.input label="Address" name="address" :value="old('address', $event?->address)" />
<x-admin.input label="Description" name="description" type="textarea" :value="old('description', $event?->description)" />
<x-admin.input label="Registration info" name="registration_info" type="textarea" :value="old('registration_info', $event?->registration_info)" />
<x-admin.input label="Image" name="image" type="file" accept="image/*" />
