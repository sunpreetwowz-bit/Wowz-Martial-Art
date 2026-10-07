@props([
    'action' => null,
    'method' => 'GET',
])

<form
    method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}"
    @if ($action) action="{{ $action }}" @endif
    {{ $attributes->merge(['class' => 'admin-panel mb-5 p-4']) }}
>
    @if (strtoupper($method) !== 'GET')
        @csrf
        @method($method)
    @endif

    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        {{ $slot }}
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <x-admin.button type="submit" variant="primary">Apply filters</x-admin.button>
        @isset($reset)
            {{ $reset }}
        @else
            <x-admin.button :href="url()->current()" variant="secondary">Reset</x-admin.button>
        @endisset
    </div>
</form>
