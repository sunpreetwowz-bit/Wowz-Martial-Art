<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Models\Event;
use App\Support\MediaUploader;
use App\Support\Slug;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Event::class, 'event');
    }

    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->input('q')) {
            $query->where('title', 'like', '%'.$request->input('q').'%');
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $events = $query->orderByDesc('start_date')->paginate(15);

        return view('admin.events.index', [
            'events' => $events,
            'statuses' => EventStatus::cases(),
        ]);
    }

    public function create()
    {
        return view('admin.events.create', ['statuses' => EventStatus::cases()]);
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Slug::unique($data['slug'] ?: $data['title'], 'events');
        $data['image_path'] = MediaUploader::store($request->file('image'), 'events');
        unset($data['image']);

        Event::query()->create($data);

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', [
            'event' => $event,
            'statuses' => EventStatus::cases(),
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $data = $request->validated();
        $data['slug'] = Slug::unique($data['slug'] ?: $data['title'], 'events', 'slug', $event->id);
        $data['image_path'] = MediaUploader::store($request->file('image'), 'events', $event->image_path);
        unset($data['image']);
        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event archived.');
    }
}
