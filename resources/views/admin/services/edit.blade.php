<x-admin-layout title="Edit Service">
    <x-slot name="header">Edit Service</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            @include('admin.services._form', ['service' => $service])
            <x-admin.button type="submit">Update</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
