<x-admin-layout title="Edit Event">
    <x-slot name="header">Edit Event</x-slot>
    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.events._form', ['event' => $event])
            <x-admin.button type="submit">Update</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
