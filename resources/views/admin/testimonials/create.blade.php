<x-admin-layout title="Add Testimonial">
    <x-slot name="header">Add Testimonial</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('admin.testimonials._form', ['testimonial' => null])
            <x-admin.button type="submit">Save</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
