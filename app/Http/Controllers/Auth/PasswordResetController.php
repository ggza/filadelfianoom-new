<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Turnstile;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function showRequest()
    {
        return view('auth.forgot');
    }

    public function sendLink(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:190',
            'cf-turnstile-response' => [new Turnstile],
        ]);

        // a válasz mindig ugyanaz, hogy ne derüljön ki, mely címek vannak regisztrálva
        Password::sendResetLink(['email' => $data['email']]);

        return back()->with('status', __('account.reset_link_sent'));
    }

    public function showReset(string $token)
    {
        return view('auth.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|max:190',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $result = Password::reset($data, function ($user, $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });

        if ($result !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __('account.reset_invalid')])->onlyInput('email');
        }

        return redirect()->route('login')->with('status', __('account.reset_done'));
    }
}
