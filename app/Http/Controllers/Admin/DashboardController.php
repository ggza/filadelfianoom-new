<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Resident;
use App\Models\User;

class DashboardController extends Controller
{
    /** A böngésző jelzése, hogy az admin még dolgozik (a munkamenet-tétlenség mérőjét frissíti). */
    public function keepalive()
    {
        return response()->json(['ok' => true]);
    }

    public function __invoke()
    {
        return view('admin.dashboard', [
            'pendingUsers' => User::where('status', 'pending')->count(),
            'newAdmissions' => Admission::where('status', 'new')->count(),
            'newMessages' => ContactMessage::where('status', 'new')->count(),
            'posts' => Post::count(),
            'residents' => Resident::where('active', true)->count(),
        ]);
    }
}
