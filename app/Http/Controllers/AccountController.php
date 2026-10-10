<?php

namespace App\Http\Controllers;

use App\Models\ResidentPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

/** A hozzátartozók saját oldala. */
class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $residents = $user->isApproved()
            ? $user->residents()->where('active', true)->with(['updates.photos', 'photos'])->get()
            : collect();

        return view('account.index', compact('user', 'residents'));
    }

    /** Privát fotó kiszolgálása, csak jogosult felhasználónak. */
    public function photo(Request $request, ResidentPhoto $photo, string $size = 'full')
    {
        abort_unless($request->user()->canSeeResident($photo->resident), 403);
        $path = $size === 'thumb' ? $photo->thumb : $photo->path;
        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), ['Cache-Control' => 'private, max-age=86400']);
    }

    public function password(Request $request)
    {
        $data = $request->validateWithBag('password', [
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', __('account.saved'));
    }
}
