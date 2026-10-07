<x-admin.input label="Name" name="name" :value="old('name', $belt?->name)" required />
<x-admin.input label="Color" name="color" :value="old('color', $belt?->color)" placeholder="#FFFFFF or White" />
<x-admin.input label="Rank order" name="rank_order" type="number" :value="old('rank_order', $belt?->rank_order ?? $nextRank)" required />
<x-admin.input label="Description" name="description" type="textarea" :value="old('description', $belt?->description)" />
<x-admin.input label="Status" name="status" type="select" required>
    @foreach ($statuses as $status)
        <option value="{{ $status->value }}" @selected(old('status', $belt?->status?->value ?? 'active') === $status->value)>
            {{ $status->label() }}
        </option>
    @endforeach
</x-admin.input>
