<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $q = Admission::latest()->latest('id');
        if ($request->filled('status')) $q->where('status', $request->status);
        return view('admin.admissions.index', ['admissions' => $q->paginate(30)->withQueryString()]);
    }

    public function show(Admission $admission)
    {
        return view('admin.admissions.show', compact('admission'));
    }

    public function update(Request $request, Admission $admission)
    {
        $admission->update($request->validate([
            'status' => ['required', Rule::in(Admission::STATUSES)],
            'admin_note' => 'nullable|string|max:5000',
        ]));
        return back()->with('status', 'Mentve.');
    }

    public function destroy(Admission $admission)
    {
        $admission->delete();
        return redirect()->route('admin.admissions.index')->with('status', 'Jelentkezés törölve.');
    }
}
