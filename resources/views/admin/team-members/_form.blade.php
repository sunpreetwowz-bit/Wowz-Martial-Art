<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Name" name="name" :value="old('name', $member?->name)" required />
    <x-admin.input label="Designation" name="designation" :value="old('designation', $member?->designation)" />
    <x-admin.input label="Display order" name="display_order" type="number" :value="old('display_order', $member?->display_order ?? 0)" />
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $member?->status?->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </x-admin.input>
</div>
<x-admin.input label="Biography" name="biography" type="textarea" :value="old('biography', $member?->biography)" />
<x-admin.input label="Photo" name="photo" type="file" accept="image/*" />
