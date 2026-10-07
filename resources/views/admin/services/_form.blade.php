<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Name" name="name" :value="old('name', $service?->name)" required />
    <x-admin.input label="Slug (optional)" name="slug" :value="old('slug', $service?->slug)" />
    <x-admin.input label="Display order" name="display_order" type="number" :value="old('display_order', $service?->display_order ?? 0)" />
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $service?->status?->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </x-admin.input>
</div>
<x-admin.input label="Description" name="description" type="textarea" :value="old('description', $service?->description)" />
<x-admin.input label="Image" name="image" type="file" accept="image/*" />
