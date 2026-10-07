<x-admin-layout title="Team Members">
    <x-slot name="header">Our Team</x-slot>
    <x-admin.page-header title="Our Team" description="Active members appear on the public website.">
        <x-slot:actions><x-admin.button :href="route('admin.team-members.create')">Add member</x-admin.button></x-slot:actions>
    </x-admin.page-header>
    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Designation</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($members as $member)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $member->name }}</td>
                    <td class="px-4 py-3">{{ $member->designation }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$member->status" /></td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.team-members.edit', $member)" variant="secondary">Edit</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$members" />
    </x-admin.card>
</x-admin-layout>
