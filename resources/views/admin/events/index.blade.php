<x-admin-layout title="Events">
    <x-slot name="header">Events</x-slot>
    <x-admin.page-header title="Events" description="Publish upcoming and past academy events.">
        <x-slot:actions><x-admin.button :href="route('admin.events.create')">Add event</x-admin.button></x-slot:actions>
    </x-admin.page-header>
    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($events as $event)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $event->title }}</td>
                    <td class="px-4 py-3">{{ $event->start_date->format('d M Y') }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$event->status" /></td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.events.edit', $event)" variant="secondary">Edit</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$events" />
    </x-admin.card>
</x-admin-layout>
