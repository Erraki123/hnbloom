<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        if ($request->filled('website')) {
            return back()->with('success', 'Merci, votre message a bien été envoyé.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
            'subject' => ['required', Rule::in(array_keys(ContactMessage::SUBJECTS))],
            'message' => 'required|string|min:10|max:3000',
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.');
    }
}
