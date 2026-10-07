<x-admin-layout title="Add Event">
    <x-slot name="header">Add Event</x-slot>
    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('admin.events._form', ['event' => null])
            <x-admin.button type="submit">Save</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
