<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Author name" name="author_name" :value="old('author_name', $testimonial?->author_name)" required />
    <x-admin.input label="Author title" name="author_title" :value="old('author_title', $testimonial?->author_title)" />
    <x-admin.input label="Rating" name="rating" type="number" :value="old('rating', $testimonial?->rating)" />
    <x-admin.input label="Display order" name="display_order" type="number" :value="old('display_order', $testimonial?->display_order ?? 0)" />
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $testimonial?->status?->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </x-admin.input>
    <div class="flex items-end pb-2">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_approved" value="1" @checked(old('is_approved', $testimonial?->is_approved)) class="rounded border-slate-300 text-red-600 focus:ring-red-500">
            Approved for public display
        </label>
    </div>
</div>
<x-admin.input label="Content" name="content" type="textarea" :value="old('content', $testimonial?->content)" required />
<x-admin.input label="Photo" name="photo" type="file" accept="image/*" />
