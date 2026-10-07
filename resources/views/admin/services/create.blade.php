<x-admin-layout title="Add Service">
    <x-slot name="header">Add Service</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('admin.services._form', ['service' => null])
            <x-admin.button type="submit">Save</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
