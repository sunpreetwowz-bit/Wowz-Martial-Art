<?php

namespace App\Http\Controllers\Web;

use App\Enums\ContactStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreContactRequest;
use App\Models\Contact;

class ContactController extends Controller
{
    public function create()
    {
        return view('web.contact');
    }

    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();
        $message = $data['message'];

        if (! empty($data['preferred_branch'])) {
            $branch = collect(config('academy.branches'))
                ->firstWhere('key', $data['preferred_branch']);

            if ($branch) {
                $message = 'Preferred branch: '.$branch['name'].' ('.$branch['area'].")\n\n".$message;
            }
        }

        Contact::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $message,
            'status' => ContactStatus::Unread,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('contact.create')
            ->with('success', 'Thank you. Your message has been sent. We will get back to you soon.');
    }
}
