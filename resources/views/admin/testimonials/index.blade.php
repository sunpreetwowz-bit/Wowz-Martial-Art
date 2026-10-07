<x-admin-layout title="Testimonials">
    <x-slot name="header">Testimonials</x-slot>
    <x-admin.page-header title="Testimonials" description="Only approved + active testimonials appear publicly.">
        <x-slot:actions><x-admin.button :href="route('admin.testimonials.create')">Add testimonial</x-admin.button></x-slot:actions>
    </x-admin.page-header>
    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Author</th>
                <th class="px-4 py-3">Approved</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($testimonials as $testimonial)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $testimonial->author_name }}</div>
                        <div class="text-xs text-slate-500 line-clamp-1">{{ $testimonial->content }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $testimonial->is_approved ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$testimonial->status" /></td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.testimonials.edit', $testimonial)" variant="secondary">Edit</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$testimonials" />
    </x-admin.card>
</x-admin-layout>
