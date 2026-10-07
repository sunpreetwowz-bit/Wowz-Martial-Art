<x-admin-layout title="Services">
    <x-slot name="header">Services</x-slot>
    <x-admin.page-header title="Services" description="Public website programs. Do not hardcode these in templates.">
        <x-slot:actions>
            <x-admin.button :href="route('admin.services.create')">Add service</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" />
        <x-admin.input label="Status" name="status" type="select">
            <option value="">All</option>
            <option value="active" @selected(request('status')==='active')>Active</option>
            <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
        </x-admin.input>
    </x-admin.filters>

    <x-admin.card :padding="false">
        @if ($services->isEmpty())
            <div class="p-6"><x-admin.empty-state title="No services" description="Create Yoga, Taekwondo and other programs." /></div>
        @else
            <x-admin.table>
                <x-slot:head>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Order</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </x-slot:head>
                @foreach ($services as $service)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $service->name }}</div>
                            <div class="text-xs text-slate-500">{{ $service->slug }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $service->display_order }}</td>
                        <td class="px-4 py-3"><x-admin.badge :status="$service->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <x-admin.button :href="route('admin.services.edit', $service)" variant="secondary">Edit</x-admin.button>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
            <x-admin.pagination :paginator="$services" />
        @endif
    </x-admin.card>
</x-admin-layout>
