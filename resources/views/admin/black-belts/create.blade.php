<x-admin-layout title="Add Black Belt">
    <x-slot name="header">Add Black Belt</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.black-belts.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('admin.black-belts._form', ['blackBelt' => null])
            <x-admin.button type="submit">Save</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
