<x-admin-layout title="Achievements">
    <x-slot name="header">Achievements</x-slot>
    <x-admin.page-header title="Achievements" description="Published achievements appear on the public site.">
        <x-slot:actions><x-admin.button :href="route('admin.achievements.create')">Add achievement</x-admin.button></x-slot:actions>
    </x-admin.page-header>
    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Student</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($achievements as $achievement)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $achievement->title }}</td>
                    <td class="px-4 py-3">{{ $achievement->student?->user?->name ?? '—' }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$achievement->status" /></td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.achievements.edit', $achievement)" variant="secondary">Edit</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$achievements" />
    </x-admin.card>
</x-admin-layout>
