<x-admin-layout title="Add Student">
    <x-slot name="header">Add Student</x-slot>

    <x-admin.page-header title="Add student" description="Creates a login account and student profile. A temporary password will be shown once." />

    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('admin.students._form', ['student' => null])

            <div class="flex gap-2">
                <x-admin.button type="submit">Create student</x-admin.button>
                <x-admin.button :href="route('admin.students.index')" variant="secondary">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin-layout>
