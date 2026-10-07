<x-admin-layout title="Edit Student">
    <x-slot name="header">Edit Student</x-slot>

    <x-admin.page-header title="Edit student" :description="$student->user->name.' · '.$student->student_code" />

    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.students._form', ['student' => $student])

            <div class="flex gap-2">
                <x-admin.button type="submit">Update student</x-admin.button>
                <x-admin.button :href="route('admin.students.show', $student)" variant="secondary">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin-layout>
