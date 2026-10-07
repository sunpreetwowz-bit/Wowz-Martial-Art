<x-admin-layout title="Contacts">
    <x-slot name="header">Contacts</x-slot>
    <x-admin.page-header title="Contacts" description="Messages submitted from the public contact form." />
    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" />
        <x-admin.input label="Status" name="status" type="select">
            <option value="">All</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-admin.input>
    </x-admin.filters>
    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">From</th>
                <th class="px-4 py-3">Subject</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Received</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($contacts as $contact)
                <tr class="{{ $contact->status->value === 'unread' ? 'bg-amber-50/40' : '' }}">
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $contact->name }}</div>
                        <div class="text-xs text-slate-500">{{ $contact->email }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $contact->subject }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$contact->status" /></td>
                    <td class="px-4 py-3 text-sm">{{ $contact->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.contacts.show', $contact)" variant="secondary">View</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$contacts" />
    </x-admin.card>
</x-admin-layout>
