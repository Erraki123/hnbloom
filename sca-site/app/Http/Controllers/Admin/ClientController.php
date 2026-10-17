<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
{
    $clients = User::where('role', 'client')->latest()->get();
    return view('admin.clients.index', compact('clients'));
}

public function approve(User $user)
{
    $user->update(['is_approved' => true]);
    return back()->with('success', 'Compte approuvé.');
}

public function block(User $user)
{
    $user->update(['is_approved' => false]);
    return back()->with('success', 'Compte bloqué.');
}
}
