<x-admin-layout title="Edit Belt Test">
    <x-slot name="header">Edit Belt Test</x-slot>
    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.belt-tests.update', $test) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.belt-tests._form', ['test' => $test, 'selectedStudentIds' => $selectedStudentIds])
            <x-admin.button type="submit">Update test</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
