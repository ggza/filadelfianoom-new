<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Nyelv: ?lang=ro|hu → munkamenet → bejelentkezett felhasználó nyelve → román (alapértelmezett). */
class SetLocale
{
    public const LANGS = ['ro', 'hu'];

    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->query('lang');
        if (in_array($lang, self::LANGS, true)) {
            $request->session()->put('lang', $lang);
            if ($request->user() && $request->user()->locale !== $lang) {
                $request->user()->forceFill(['locale' => $lang])->save();
            }
        } else {
            $lang = $request->session()->get('lang', $request->user()->locale ?? 'ro');
        }
        app()->setLocale(in_array($lang, self::LANGS, true) ? $lang : 'ro');

        return $next($request);
    }
}
