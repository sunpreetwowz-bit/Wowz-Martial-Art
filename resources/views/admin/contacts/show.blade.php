<x-admin-layout title="Contact Message">
    <x-slot name="header">Contact Message</x-slot>
    <x-admin.page-header :title="$contact->subject" :description="$contact->name.' · '.$contact->email">
        <x-slot:actions>
            <x-admin.button :href="route('admin.contacts.index')" variant="secondary">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>
    <x-admin.card>
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-500">Phone</dt><dd>{{ $contact->phone ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">Status</dt><dd><x-admin.badge :status="$contact->status" /></dd></div>
            <div><dt class="text-slate-500">Received</dt><dd>{{ $contact->created_at->format('d M Y H:i') }}</dd></div>
            <div><dt class="text-slate-500">IP</dt><dd>{{ $contact->ip_address ?: '—' }}</dd></div>
        </dl>
        <div class="mt-6 whitespace-pre-line rounded-lg bg-slate-50 p-4 text-slate-800">{{ $contact->message }}</div>
        <div class="mt-6 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.contacts.unread', $contact) }}">@csrf
                <x-admin.button type="submit" variant="secondary">Mark unread</x-admin.button>
            </form>
            <form method="POST" action="{{ route('admin.contacts.archive', $contact) }}">@csrf
                <x-admin.button type="submit" variant="secondary">Archive</x-admin.button>
            </form>
            <x-admin.confirm-form :action="route('admin.contacts.destroy', $contact)" title="Delete contact?">
                <x-slot:trigger><x-admin.button type="button" variant="danger">Delete</x-admin.button></x-slot:trigger>
            </x-admin.confirm-form>
        </div>
    </x-admin.card>
</x-admin-layout>
