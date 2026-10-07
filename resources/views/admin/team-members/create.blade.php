<x-admin-layout title="Add Team Member">
    <x-slot name="header">Add Team Member</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.team-members.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('admin.team-members._form', ['member' => null])
            <x-admin.button type="submit">Save</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
