<x-admin-layout title="Edit Belt">
    <x-slot name="header">Edit Belt</x-slot>

    <x-admin.page-header title="Edit belt" :description="$belt->name" />

    <x-admin.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.belts.update', $belt) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('admin.belts._form', ['belt' => $belt, 'nextRank' => $belt->rank_order])

            <div class="flex gap-2 pt-2">
                <x-admin.button type="submit">Update belt</x-admin.button>
                <x-admin.button :href="route('admin.belts.index')" variant="secondary">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin-layout>
