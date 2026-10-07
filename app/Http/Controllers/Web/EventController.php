<?php

namespace App\Http\Controllers\Web;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $upcoming = Event::query()
            ->published()
            ->whereDate('start_date', '>=', $today)
            ->orderBy('start_date')
            ->get();

        $past = Event::query()
            ->published()
            ->whereDate('start_date', '<', $today)
            ->orderByDesc('start_date')
            ->paginate(9);

        return view('web.events.index', [
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }

    public function show(Event $event)
    {
        if ($event->status !== EventStatus::Published) {
            abort(404);
        }

        $related = Event::query()
            ->published()
            ->where('id', '!=', $event->id)
            ->orderByDesc('start_date')
            ->take(3)
            ->get();

        return view('web.events.show', [
            'event' => $event,
            'related' => $related,
        ]);
    }
}
