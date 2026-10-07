<x-admin-layout title="Edit About Section">
    <x-slot name="header">Edit About Section</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.about.update', $section) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <p class="text-sm text-slate-500">Key: <span class="font-mono">{{ $section->key }}</span></p>
            <x-admin.input label="Title" name="title" :value="old('title', $section->title)" required />
            <x-admin.input label="Content" name="content" type="textarea" :value="old('content', $section->content)" />
            <x-admin.input label="Display order" name="display_order" type="number" :value="old('display_order', $section->display_order)" />
            <x-admin.input label="Status" name="status" type="select" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $section->status->value) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </x-admin.input>
            <x-admin.input label="Image" name="image" type="file" accept="image/*" />
            <x-admin.button type="submit">Update</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
