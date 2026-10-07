@props([
    'action',
    'title' => 'Confirm delete',
    'message' => 'Are you sure you want to delete this record? This action cannot be easily undone.',
    'confirmLabel' => 'Delete',
    'method' => 'DELETE',
])

<div
    x-data="{ open: false }"
    {{ $attributes }}
>
    <span @click="open = true">
        {{ $trigger ?? $slot }}
    </span>

    <template x-teleport="body">
        <div
            x-show="open"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-slate-900/50" @click="open = false"></div>

            <div
                class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
                @keydown.escape.window="open = false"
            >
                <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ $message }}</p>

                <form method="POST" action="{{ $action }}" class="mt-6 flex justify-end gap-2">
                    @csrf
                    @method($method)

                    <x-admin.button type="button" variant="secondary" @click="open = false">
                        Cancel
                    </x-admin.button>
                    <x-admin.button type="submit" variant="danger">
                        {{ $confirmLabel }}
                    </x-admin.button>
                </form>
            </div>
        </div>
    </template>
</div>
