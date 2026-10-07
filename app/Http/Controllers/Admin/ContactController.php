<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactStatus;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Contact::class);

        $query = Contact::query();

        if ($request->input('q')) {
            $search = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('subject', 'like', $search);
            });
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $contacts = $query->latest()->paginate(20);

        return view('admin.contacts.index', [
            'contacts' => $contacts,
            'statuses' => ContactStatus::cases(),
        ]);
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        if ($contact->status === ContactStatus::Unread) {
            $contact->update([
                'status' => ContactStatus::Read,
                'read_at' => now(),
            ]);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function markUnread(Contact $contact)
    {
        $this->authorize('update', $contact);
        $contact->update(['status' => ContactStatus::Unread, 'read_at' => null]);

        return back()->with('success', 'Marked as unread.');
    }

    public function archive(Contact $contact)
    {
        $this->authorize('update', $contact);
        $contact->update(['status' => ContactStatus::Archived]);

        return back()->with('success', 'Contact archived.');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Contact deleted.');
    }
}
