<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notifications</h2>
            @if (auth()->user()->unreadNotifications->isNotEmpty())
                <form method="POST" action="{{ route('student.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-red-700 hover:underline">Mark all read</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-3 sm:px-6 lg:px-8">
            @forelse ($notifications as $notification)
                <div @class([
                    'bg-white p-5 shadow-sm sm:rounded-lg',
                    'ring-1 ring-red-100' => is_null($notification->read_at),
                ])>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-slate-900">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                                @if (is_null($notification->read_at))
                                    <span class="rounded bg-red-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-red-700">New</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-slate-600">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <form method="POST" action="{{ route('student.notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-red-700 hover:underline">
                                {{ ($notification->data['action_url'] ?? null) ? 'Open' : 'Mark read' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 text-sm text-slate-500 shadow-sm sm:rounded-lg">No notifications yet.</div>
            @endforelse
            <div>{{ $notifications->links() }}</div>
        </div>
    </div>
</x-app-layout>
