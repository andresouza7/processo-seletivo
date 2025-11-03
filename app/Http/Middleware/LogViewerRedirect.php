<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogViewerRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            abort(403);
            // return redirect()->route('filament.admin.auth.login');
        }

        return $next($request);
    }
}
