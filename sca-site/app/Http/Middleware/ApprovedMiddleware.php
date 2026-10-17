<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
 public function handle(Request $request, Closure $next)
{
    $user = auth()->user();

    // L'admin passe toujours
    if ($user && $user->isAdmin()) {
        return $next($request);
    }

    // Client non approuvé -> page d'attente
    if ($user && !$user->is_approved) {
        return redirect()->route('pending');
    }

    return $next($request);
}
}
