<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

// Admin belépés második lépése: e-mailben küldött 6 jegyű kód, opcionálisan 30 napig megjegyzett eszköz
class AdminCodeController extends Controller
{
    private const SESSION = 'admin_mfa';
    private const COOKIE = 'admin_trusted';
    private const TTL_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;

    public static function required(User $user, Request $request): bool
    {
        return $user->isAdmin() && config('app.admin_mfa') && ! self::trusted($user, $request);
    }

    /** Jelszó után: kód küldése és átirányítás a kódoldalra (a felhasználó ekkor még nincs belépve). */
    public static function start(User $user, bool $remember, Request $request)
    {
        $request->session()->put(self::SESSION, ['user' => $user->id, 'remember' => $remember]);
        if (! (new self)->issueCode($request)) {
            $request->session()->forget(self::SESSION);
            return back()->withErrors(['email' => __('account.mfa_send_failed')])->onlyInput('email');
        }

        return redirect()->route('login.code');
    }

    public function show(Request $request)
    {
        if (! $request->session()->has(self::SESSION)) {
            return redirect()->route('login');
        }

        return view('auth.code', ['days' => config('app.admin_mfa_trust_days')]);
    }

    public function verify(Request $request)
    {
        $state = $request->session()->get(self::SESSION);
        if (! $state || empty($state['hash'])) {
            return redirect()->route('login');
        }

        $request->validate(['code' => 'required|string|max:12']);
        $code = preg_replace('/\D/', '', $request->input('code'));

        if (now()->timestamp > $state['expires']) {
            return back()->withErrors(['code' => __('account.mfa_expired')]);
        }

        if (! hash_equals($state['hash'], $this->hash($state['user'], $code))) {
            $state['attempts']++;
            if ($state['attempts'] >= self::MAX_ATTEMPTS) {
                $request->session()->forget(self::SESSION);
                return redirect()->route('login')->withErrors(['email' => __('account.mfa_too_many')]);
            }
            $request->session()->put(self::SESSION, $state);
            return back()->withErrors(['code' => __('account.mfa_wrong')]);
        }

        $user = User::find($state['user']);
        $request->session()->forget(self::SESSION);
        if (! $user || ! $user->isAdmin() || $user->status === 'blocked') {
            return redirect()->route('login');
        }

        Auth::login($user, $state['remember']);
        $request->session()->regenerate();

        if ($request->boolean('trust')) {
            $days = config('app.admin_mfa_trust_days');
            Cookie::queue(self::COOKIE, self::trustToken($user, now()->addDays($days)->timestamp), $days * 24 * 60);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function resend(Request $request)
    {
        if (! $request->session()->has(self::SESSION)) {
            return redirect()->route('login');
        }
        if (! $this->issueCode($request)) {
            return back()->withErrors(['code' => __('account.mfa_send_failed')]);
        }

        return back()->with('status', __('account.mfa_resent'));
    }

    private function issueCode(Request $request): bool
    {
        $state = $request->session()->get(self::SESSION);
        $user = User::find($state['user']);
        if (! $user) {
            return false;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $request->session()->put(self::SESSION, array_merge($state, [
            'hash' => $this->hash($user->id, $code),
            'expires' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
            'attempts' => 0,
        ]));

        $lang = in_array($user->locale, ['hu', 'ro'], true) ? $user->locale : 'hu';

        return $this->sendMail(
            $user->email,
            __('account.mfa_mail_subject', [], $lang),
            __('account.mfa_mail_body', ['name' => $user->name, 'code' => $code, 'minutes' => self::TTL_MINUTES], $lang),
        );
    }

    private function hash(int $userId, string $code): string
    {
        return hash_hmac('sha256', $userId . '|' . $code, config('app.key'));
    }

    // a megbízható eszköz jele a jelszóhoz is kötött: jelszócserekor érvényét veszti
    private static function trustToken(User $user, int $until): string
    {
        return $user->id . '|' . $until . '|' . hash_hmac('sha256', $user->id . '|' . $until . '|' . $user->password, config('app.key'));
    }

    private static function trusted(User $user, Request $request): bool
    {
        $parts = explode('|', (string) $request->cookie(self::COOKIE), 3);
        if (count($parts) !== 3 || (int) $parts[0] !== $user->id || (int) $parts[1] < now()->timestamp) {
            return false;
        }

        return hash_equals(self::trustToken($user, (int) $parts[1]), implode('|', $parts));
    }
}
