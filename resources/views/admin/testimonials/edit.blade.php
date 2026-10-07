<x-admin-layout title="Edit Testimonial">
    <x-slot name="header">Edit Testimonial</x-slot>
    <x-admin.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.testimonials._form', ['testimonial' => $testimonial])
            <x-admin.button type="submit">Update</x-admin.button>
        </form>
    </x-admin.card>
</x-admin-layout>
