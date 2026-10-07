<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Name" name="name" :value="old('name', $blackBelt?->name)" required />
    <x-admin.input label="Rank" name="rank" :value="old('rank', $blackBelt?->rank ?? '1st Dan')" required placeholder="e.g. 1st Dan, 2nd Dan" />
    <x-admin.input label="Promoted on" name="promoted_on" type="date" :value="old('promoted_on', optional($blackBelt?->promoted_on)->format('Y-m-d'))" />
    <div>
        <label class="mb-1 block text-sm font-medium text-ink">Branch</label>
        <select name="branch" class="w-full rounded-none border-line focus:border-crimson focus:ring-crimson">
            <option value="">Select branch (optional)</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch['name'] }} — {{ $branch['area'] }}" @selected(old('branch', $blackBelt?->branch) === $branch['name'].' — '.$branch['area'])>
                    {{ $branch['name'] }} — {{ $branch['area'] }}
                </option>
            @endforeach
        </select>
    </div>
    <x-admin.input label="Display order" name="display_order" type="number" :value="old('display_order', $blackBelt?->display_order ?? 0)" />
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $blackBelt?->status?->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </x-admin.input>
</div>
<x-admin.input label="Biography / journey" name="biography" type="textarea" :value="old('biography', $blackBelt?->biography)" />
<x-admin.input label="Photo" name="photo" type="file" accept="image/*" />
@if ($blackBelt?->photo_path)
    <p class="text-xs text-stoneish">Current photo on file. Upload a new image to replace it.</p>
@endif
