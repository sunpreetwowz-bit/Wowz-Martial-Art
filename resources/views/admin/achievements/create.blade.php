<x-admin-layout title="Add Achievement">
    <x-slot name="header">Add Achievement</x-slot>
    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.achievements.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('admin.achievements._form', ['achievement' => null])
            <x-admin.button type="submit">Save</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
