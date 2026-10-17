<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = ContactMessage::query()
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->subject, fn ($q, $s) => $q->where('subject', $s))
            ->when($request->q, function ($q, $s) {
                $q->where(function ($w) use ($s) {
                    $w->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%")
                        ->orWhere('company', 'like', "%{$s}%")
                        ->orWhere('message', 'like', "%{$s}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function updateStatus(Request $request, ContactMessage $message)
    {
        $request->validate([
            'status' => ['required', Rule::in(array_keys(ContactMessage::STATUSES))],
        ]);

        $message->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message supprimé.');
    }
}
