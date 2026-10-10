<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $q = ContactMessage::latest();
        if ($request->filled('status')) $q->where('status', $request->status);
        return view('admin.messages.index', ['messages' => $q->paginate(30)->withQueryString()]);
    }

    public function show(ContactMessage $message)
    {
        return view('admin.messages.show', compact('message'));
    }

    public function update(Request $request, ContactMessage $message)
    {
        $message->update($request->validate([
            'status' => ['required', Rule::in(ContactMessage::STATUSES)],
            'admin_note' => 'nullable|string|max:5000',
        ]));
        return back()->with('status', 'Mentve.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.messages.index')->with('status', 'Üzenet törölve.');
    }
}
