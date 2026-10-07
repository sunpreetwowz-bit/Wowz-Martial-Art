<x-admin-layout title="Notifications">
    <x-slot name="header">Notifications</x-slot>
    <x-admin.page-header title="Notifications" description="Important academy events for administrators.">
        <x-slot name="actions">
            @if (auth()->user()->unreadNotifications->isNotEmpty())
                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                    @csrf
                    <x-admin.button type="submit" variant="secondary">Mark all read</x-admin.button>
                </form>
            @endif
        </x-slot>
    </x-admin.page-header>

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            <x-admin.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-slate-900">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                            @if (is_null($notification->read_at))
                                <span class="rounded bg-red-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-red-700">New</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-slate-600">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                        @csrf
                        <x-admin.button type="submit" variant="secondary">
                            {{ ($notification->data['action_url'] ?? null) ? 'Open' : 'Mark read' }}
                        </x-admin.button>
                    </form>
                </div>
            </x-admin.card>
        @empty
            <x-admin.card>
                <p class="text-sm text-slate-500">No notifications yet.</p>
            </x-admin.card>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
</x-admin-layout>
