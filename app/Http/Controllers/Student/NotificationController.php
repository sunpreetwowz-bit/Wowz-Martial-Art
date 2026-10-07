<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Support\SafeRedirect;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(20);

        return view('student.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, string $notification)
    {
        $record = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $record->markAsRead();

        $url = SafeRedirect::internal($record->data['action_url'] ?? null, '');

        if ($url !== '') {
            return redirect()->to($url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
