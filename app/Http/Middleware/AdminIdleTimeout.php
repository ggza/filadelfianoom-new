<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Az admin munkamenete tétlenség után lejár (ADMIN_IDLE_MINUTES, alapértelmezés: 30 perc). */
class AdminIdleTimeout
{
    /** A böngésző csak tevékenység közben, ritkán jelez; ennyi türelmi idő kell a szerveren. */
    private const GRACE_SECONDS = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $limit = max(1, (int) config('app.admin_idle_minutes')) * 60 + self::GRACE_SECONDS;
        $last = (int) $request->session()->get('admin_last_activity', 0);

        if ($last && time() - $last > $limit) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['expired' => true], 401)
                : redirect()->route('login', ['idle' => 1]);
        }

        $request->session()->put('admin_last_activity', time());

        return $next($request);
    }
}
