<x-admin-layout title="Payments">
    <x-slot name="header">Payments</x-slot>
    <x-admin.page-header title="Payments" description="Offline cash stays pending until verified. Online statuses must be confirmed server-side." />
    <x-admin.filters>
        <x-admin.input label="Status" name="status" type="select">
            <option value="">All</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-admin.input>
        <x-admin.input label="Method" name="method" type="select">
            <option value="">All</option>
            <option value="cash" @selected(request('method')==='cash')>Cash</option>
            <option value="upi" @selected(request('method')==='upi')>UPI</option>
            <option value="paytm" @selected(request('method')==='paytm')>Paytm</option>
        </x-admin.input>
    </x-admin.filters>
    <x-admin.card :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Student</th>
                <th class="px-4 py-3">Test</th>
                <th class="px-4 py-3">Method</th>
                <th class="px-4 py-3">Amount</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($payments as $payment)
                <tr>
                    <td class="px-4 py-3">{{ $payment->student->user->name }}</td>
                    <td class="px-4 py-3">{{ $payment->application?->beltTest?->title }}</td>
                    <td class="px-4 py-3">{{ $payment->method->label() }}</td>
                    <td class="px-4 py-3">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-3"><x-admin.badge :status="$payment->status" /></td>
                    <td class="px-4 py-3 text-right"><x-admin.button :href="route('admin.payments.show', $payment)" variant="secondary">View</x-admin.button></td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$payments" />
    </x-admin.card>
</x-admin-layout>
