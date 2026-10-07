<x-admin-layout title="Certificates">
    <x-slot name="header">Certificates</x-slot>
    <x-admin.page-header title="Certificates" description="Issued certificates can be verified publicly using the certificate number and verification code." />

    <x-admin.filters>
        <x-admin.input label="Search" name="q" :value="request('q')" placeholder="Number, name, or student code" />
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
                <th class="px-4 py-3">Certificate</th>
                <th class="px-4 py-3">Student</th>
                <th class="px-4 py-3">Belt</th>
                <th class="px-4 py-3">Issued</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @forelse ($certificates as $certificate)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs">{{ $certificate->certificate_number }}</td>
                    <td class="px-4 py-3">{{ $certificate->student_name_snapshot }}</td>
                    <td class="px-4 py-3">{{ $certificate->belt_name_snapshot }}</td>
                    <td class="px-4 py-3">{{ optional($certificate->issued_on)->format('d M Y') }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$certificate->status" /></td>
                    <td class="px-4 py-3 text-right">
                        <x-admin.button :href="route('admin.certificates.show', $certificate)" variant="secondary">View</x-admin.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No certificates yet.</td>
                </tr>
            @endforelse
        </x-admin.table>
        <x-admin.pagination :paginator="$certificates" />
    </x-admin.card>
</x-admin-layout>
