<x-admin-layout title="Dashboard">
    <x-slot name="header">Dashboard</x-slot>

    <div class="relative mb-8 overflow-hidden bg-navy text-white">
        <div class="pointer-events-none absolute inset-0 opacity-30" style="background: linear-gradient(115deg, transparent 40%, rgba(208,18,48,0.45) 100%);"></div>
        <div class="relative grid gap-6 p-7 sm:p-9 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-8">
                <p class="section-label">Wowz Martial Art</p>
                <h1 class="font-display mt-2 text-3xl uppercase tracking-[0.06em] sm:text-5xl">Academy control centre</h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/65">
                    Review applications, verify payments, manage belt tests, and keep the public website content fresh.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 lg:col-span-4 lg:justify-end">
                <a href="{{ route('admin.belt-test-applications.index') }}" class="admin-btn-primary">Applications</a>
                <a href="{{ route('admin.payments.index') }}" class="admin-btn-secondary !border-white/20 !bg-transparent !text-white hover:!border-crimson hover:!bg-crimson">Payments</a>
                <a href="{{ route('admin.services.index') }}" class="admin-btn-secondary !border-white/20 !bg-transparent !text-white hover:!border-crimson hover:!bg-crimson">Website</a>
            </div>
        </div>
    </div>

    <x-admin.page-header
        title="Overview"
        description="Live counts from students, tests, payments, and website inbox."
    />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-admin.stat-card label="Total students" :value="$stats['total_students']" tone="slate" />
        <x-admin.stat-card label="Active students" :value="$stats['active_students']" hint="Currently enrolled" tone="emerald" />
        <x-admin.stat-card label="Upcoming belt tests" :value="$stats['upcoming_belt_tests']" tone="sky" />
        <x-admin.stat-card label="Pending applications" :value="$stats['pending_applications']" tone="amber" />
        <x-admin.stat-card label="Pending payments" :value="$stats['pending_payments']" tone="amber" />
        <x-admin.stat-card label="Passed tests" :value="$stats['passed_tests']" tone="emerald" />
        <x-admin.stat-card label="Failed tests" :value="$stats['failed_tests']" tone="red" />
        <x-admin.stat-card label="Certificates" :value="$stats['total_certificates']" tone="slate" />
        <x-admin.stat-card label="Unread contacts" :value="$stats['unread_contacts']" tone="sky" />
        <x-admin.stat-card label="Upcoming events" :value="$stats['upcoming_events']" tone="slate" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="admin-panel">
            <div class="flex items-center justify-between border-b border-line bg-paper/70 px-4 py-3.5">
                <h2 class="font-display text-sm uppercase tracking-[0.12em] text-ink">Applications queue</h2>
                <a href="{{ route('admin.belt-test-applications.index') }}" class="text-xs font-semibold uppercase tracking-[0.12em] text-crimson hover:text-crimson-dark">View all</a>
            </div>
            <div class="divide-y divide-line">
                @forelse ($queues['applications'] as $application)
                    <a href="{{ route('admin.belt-test-applications.show', $application) }}" class="block px-4 py-3.5 text-sm transition hover:bg-paper">
                        <div class="font-semibold text-ink">{{ $application->student->user->name }}</div>
                        <div class="text-xs text-stoneish">{{ $application->beltTest->title }}</div>
                        <div class="mt-1.5"><x-admin.badge :status="$application->status" /></div>
                    </a>
                @empty
                    <p class="px-4 py-8 text-sm text-stoneish">No pending applications.</p>
                @endforelse
            </div>
        </section>

        <section class="admin-panel">
            <div class="flex items-center justify-between border-b border-line bg-paper/70 px-4 py-3.5">
                <h2 class="font-display text-sm uppercase tracking-[0.12em] text-ink">Payments to verify</h2>
                <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold uppercase tracking-[0.12em] text-crimson hover:text-crimson-dark">View all</a>
            </div>
            <div class="divide-y divide-line">
                @forelse ($queues['payments'] as $payment)
                    <a href="{{ route('admin.payments.show', $payment) }}" class="block px-4 py-3.5 text-sm transition hover:bg-paper">
                        <div class="font-semibold text-ink">{{ $payment->student->user->name }}</div>
                        <div class="text-xs text-stoneish">
                            {{ $payment->method->label() }} · {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                        </div>
                    </a>
                @empty
                    <p class="px-4 py-8 text-sm text-stoneish">No pending payments.</p>
                @endforelse
            </div>
        </section>

        <section class="admin-panel">
            <div class="flex items-center justify-between border-b border-line bg-paper/70 px-4 py-3.5">
                <h2 class="font-display text-sm uppercase tracking-[0.12em] text-ink">Unread contacts</h2>
                <a href="{{ route('admin.contacts.index') }}" class="text-xs font-semibold uppercase tracking-[0.12em] text-crimson hover:text-crimson-dark">View all</a>
            </div>
            <div class="divide-y divide-line">
                @forelse ($queues['contacts'] as $contact)
                    <a href="{{ route('admin.contacts.show', $contact) }}" class="block px-4 py-3.5 text-sm transition hover:bg-paper">
                        <div class="font-semibold text-ink">{{ $contact->name }}</div>
                        <div class="truncate text-xs text-stoneish">{{ $contact->subject ?: $contact->email }}</div>
                    </a>
                @empty
                    <p class="px-4 py-8 text-sm text-stoneish">Inbox is clear.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-admin-layout>
