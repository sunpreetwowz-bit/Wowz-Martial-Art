<x-admin-layout title="Edit Competition Form">
    <x-slot name="header">Edit form</x-slot>
    <x-admin.page-header :title="$form->title" description="Update details, replace PDF, or change student assignments." />
    <x-admin.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.competition-forms.update', $form) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            @include('admin.competition-forms._form', ['form' => $form, 'selectedStudentIds' => $selectedStudentIds])
            <div class="flex gap-3">
                <x-admin.button type="submit">Save changes</x-admin.button>
                <x-admin.button :href="route('admin.competition-forms.show', $form)" variant="secondary">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin-layout>
