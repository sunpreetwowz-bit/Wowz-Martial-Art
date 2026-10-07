<x-admin-layout title="Edit Team Member">
    <x-slot name="header">Edit Team Member</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.team-members.update', $member) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.team-members._form', ['member' => $member])
            <x-admin.button type="submit">Update</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
