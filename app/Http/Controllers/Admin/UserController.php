<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('residents')->orderByRaw("status = 'pending' desc")->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user->load('residents'),
            'residents' => Resident::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:40',
            'status' => ['required', Rule::in(['pending', 'approved', 'blocked'])],
            'role' => ['required', Rule::in(['relative', 'admin'])],
            'residents' => 'array',
            'residents.*' => 'integer|exists:residents,id',
            'relation' => 'array',
        ]);
        // saját magát ne tudja kizárni az admin
        if ($user->is($request->user())) { $data['role'] = 'admin'; $data['status'] = 'approved'; }

        $justApproved = $user->status !== 'approved' && $data['status'] === 'approved';

        $user->update(collect($data)->only(['name', 'phone', 'status', 'role'])->all());

        $sync = [];
        foreach ($data['residents'] ?? [] as $rid) {
            $sync[$rid] = ['relation' => $request->input("relation.$rid")];
        }
        $user->residents()->sync($sync);

        $status = 'Felhasználó mentve: ' . $user->name;

        // jóváhagyáskor értesítjük a hozzátartozót a saját nyelvén
        if ($justApproved) {
            $lang = in_array($user->locale, ['hu', 'ro'], true) ? $user->locale : 'hu';
            $sent = $this->sendMail(
                $user->email,
                __('account.approved_subject', [], $lang),
                __('account.approved_body', ['name' => $user->name, 'url' => route('login')], $lang),
            );
            $status .= $sent ? ' – jóváhagyó levél elküldve.' : ' – a jóváhagyó levelet nem sikerült elküldeni (részletek a naplóban).';
        }

        return redirect()->route('admin.users.index')->with('status', $status);
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 422, 'Saját magát nem törölheti.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'Felhasználó törölve.');
    }
}
