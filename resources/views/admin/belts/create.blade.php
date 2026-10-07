<x-admin-layout title="Add Belt">
    <x-slot name="header">Add Belt</x-slot>

    <x-admin.page-header title="Add belt" description="Define a new grading rank for the academy." />

    <x-admin.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.belts.store') }}" class="space-y-4">
            @csrf
            @include('admin.belts._form', ['belt' => null, 'nextRank' => $nextRank])

            <div class="flex gap-2 pt-2">
                <x-admin.button type="submit">Save belt</x-admin.button>
                <x-admin.button :href="route('admin.belts.index')" variant="secondary">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin-layout>
