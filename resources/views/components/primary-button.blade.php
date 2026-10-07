<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center bg-crimson px-6 py-3 text-sm font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-crimson-dark focus:outline-none focus:ring-2 focus:ring-crimson focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
