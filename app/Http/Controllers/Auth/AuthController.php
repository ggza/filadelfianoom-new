<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate(['email' => 'required|email', 'password' => 'required|string']);

        // jelszó ellenőrzése belépés nélkül; az adminnak még a biztonsági kód is kell
        if (! Auth::validate($cred)) {
            return back()->withErrors(['email' => __('account.failed')])->onlyInput('email');
        }
        $user = Auth::getLastAttempted();
        if ($user->status !== 'blocked' && AdminCodeController::required($user, $request)) {
            return AdminCodeController::start($user, $request->boolean('remember'), $request);
        }

        Auth::login($user, $request->boolean('remember'));
        if (Auth::user()->status === 'blocked') {
            Auth::logout();
            return back()->withErrors(['email' => __('account.blocked')])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(Auth::user()->isAdmin() ? route('admin.dashboard') : route('account'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190|unique:users,email',
            'phone' => 'nullable|string|max:40',
            'resident_name' => 'required|string|max:120',
            'relation' => 'nullable|string|max:60',
            'password' => ['required', 'confirmed', Password::min(8)],
            'consent' => 'accepted',
            'cf-turnstile-response' => [new Turnstile],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => 'relative',
            'status' => 'pending',
            'locale' => app()->getLocale(),
            'requested_resident' => $data['resident_name'],
            'requested_relation' => $data['relation'] ?? null,
        ]);

        $this->notifyAdmin(
            'Új regisztráció',
            "Új hozzátartozói regisztráció vár jóváhagyásra.\n\nNév: {$user->name}\nE-mail: {$user->email}\n\n"
            . route('admin.users.edit', $user),
            $user->email,
            $user->name,
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
