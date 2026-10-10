<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return view('admin.sections.index', ['sections' => Section::orderBy('sort')->get()]);
    }

    public function edit(Section $section)
    {
        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'title_ro' => 'nullable|string|max:255',
            'title_hu' => 'nullable|string|max:255',
            'body_ro' => 'nullable|string',
            'body_hu' => 'nullable|string',
        ]);
        $data['show_ro'] = $request->boolean('show_ro');
        $data['show_hu'] = $request->boolean('show_hu');
        $section->update($data);

        return redirect()->route('admin.sections.index')->with('status', 'Mentve: ' . $section->admin_label);
    }
}
