<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $lastActivity = $request->session()->get('last_activity');
        $timeout = config('session.lifetime') * 60; // dalam detik

        if ($lastActivity && (time() - $lastActivity > $timeout)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('message', 'Session Anda telah habis karena tidak aktif.');
        }

        $request->session()->put('last_activity', time());

        return $next($request);
    }
}
