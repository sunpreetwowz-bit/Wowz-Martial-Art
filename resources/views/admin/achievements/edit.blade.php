<x-admin-layout title="Edit Achievement">
    <x-slot name="header">Edit Achievement</x-slot>
    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.achievements.update', $achievement) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.achievements._form', ['achievement' => $achievement])
            <x-admin.button type="submit">Update</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
