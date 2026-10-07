<x-admin-layout title="Our Black Belts">
    <x-slot name="header">Our Black Belts</x-slot>
    <x-admin.page-header title="Our Black Belts" description="Students promoted to black belt. Active profiles appear on the public website.">
        <x-slot:actions><x-admin.button :href="route('admin.black-belts.create')">Add black belt</x-admin.button></x-slot:actions>
    </x-admin.page-header>
    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Rank</th>
                <th class="px-4 py-3">Promoted</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($blackBelts as $blackBelt)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $blackBelt->name }}</td>
                    <td class="px-4 py-3">{{ $blackBelt->rank }}</td>
                    <td class="px-4 py-3 text-sm">{{ $blackBelt->promoted_on?->format('M Y') ?: '—' }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$blackBelt->status" /></td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.black-belts.edit', $blackBelt)" variant="secondary">Edit</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$blackBelts" />
    </x-admin.card>
</x-admin-layout>
