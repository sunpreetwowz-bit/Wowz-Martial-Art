@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
])

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-stoneish">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            @if ($required) required @endif
            rows="4"
            {{ $attributes->merge(['class' => 'block w-full border-line shadow-none focus:border-crimson focus:ring-crimson']) }}
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        >{{ old($name, $value) }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @if ($required) required @endif
            {{ $attributes->merge(['class' => 'block w-full border-line shadow-none focus:border-crimson focus:ring-crimson']) }}
        >
            {{ $slot }}
        </select>
    @elseif ($type === 'file')
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="file"
            @if ($required) required @endif
            {{ $attributes->merge(['class' => 'block w-full border-line text-sm shadow-none focus:border-crimson focus:ring-crimson']) }}
        />
    @else
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            @if ($required) required @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            {{ $attributes->merge(['class' => 'block w-full border-line shadow-none focus:border-crimson focus:ring-crimson']) }}
        />
    @endif

    @error($name)
        <p class="mt-1 text-sm text-crimson">{{ $message }}</p>
    @enderror
</div>
