<x-admin-layout title="Edit Black Belt">
    <x-slot name="header">Edit Black Belt</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.black-belts.update', $blackBelt) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.black-belts._form', ['blackBelt' => $blackBelt])
            <x-admin.button type="submit">Update</x-admin.button>
        </form>
        <div class="mt-6 border-t border-line pt-4">
            <x-admin.confirm-form
                :action="route('admin.black-belts.destroy', $blackBelt)"
                title="Archive this black belt profile?"
                message="The profile will be hidden from the public website."
                confirmLabel="Archive"
            >
                <x-admin.button type="button" variant="secondary">Archive</x-admin.button>
            </x-admin.confirm-form>
        </div>
    </x-admin.card>
</x-admin-layout>
