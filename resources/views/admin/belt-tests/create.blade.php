<x-admin-layout title="Create Belt Test">
    <x-slot name="header">Create Belt Test</x-slot>
    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.belt-tests.store') }}" class="space-y-4">
            @csrf
            @include('admin.belt-tests._form', ['test' => null, 'selectedStudentIds' => []])
            <x-admin.button type="submit">Create test</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
