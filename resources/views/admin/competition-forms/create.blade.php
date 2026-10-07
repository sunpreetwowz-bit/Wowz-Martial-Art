<x-admin-layout title="Upload Competition Form">
    <x-slot name="header">Upload form</x-slot>
    <x-admin.page-header title="Upload competition form" description="PDF is stored privately. Only assigned students (and admins) can download it." />
    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.competition-forms.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('admin.competition-forms._form', ['form' => null, 'selectedStudentIds' => []])
            <x-admin.button type="submit">Create & assign</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
