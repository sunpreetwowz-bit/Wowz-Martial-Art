<x-admin-layout title="About Us">
    <x-slot name="header">About Us</x-slot>
    <x-admin.page-header title="About sections" description="Manage Who We Are, Mission, Vision, Goals and philosophy content." />
    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Key</th>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($sections as $section)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs">{{ $section->key }}</td>
                    <td class="px-4 py-3">{{ $section->title }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$section->status" /></td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.about.edit', $section)" variant="secondary">Edit</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
    </x-admin.card>
</x-admin-layout>
