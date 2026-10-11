<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Support\ImageTool;
use Illuminate\Http\Request;
use Throwable;

class GalleryController extends Controller
{
    public function index()
    {
        return view('admin.gallery.index', ['images' => GalleryImage::orderBy('sort')->orderBy('id')->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:30',
            'images.*' => 'image|max:20480',
            'caption_hu' => 'nullable|string|max:255',
            'caption_ro' => 'nullable|string|max:255',
        ]);
        $sort = (int) GalleryImage::max('sort');
        $done = 0;
        foreach ($request->file('images') as $file) {
            try {
                $img = ImageTool::store($file, public_path('uploads/galeria'), 'uploads/galeria/');
            } catch (Throwable $e) {
                return back()->withErrors(['images' => $file->getClientOriginalName() . ': ' . $e->getMessage()]);
            }
            GalleryImage::create($img + [
                'caption_hu' => $request->input('caption_hu'),
                'caption_ro' => $request->input('caption_ro'),
                'sort' => ++$sort,
            ]);
            $done++;
        }

        return back()->with('status', "$done kép feltöltve.");
    }

    /** Feliratok, láthatóság és sorrend mentése egyszerre. */
    public function updateAll(Request $request)
    {
        $rows = $request->validate([
            'img' => 'array',
            'img.*.caption_hu' => 'nullable|string|max:255',
            'img.*.caption_ro' => 'nullable|string|max:255',
            'img.*.sort' => 'nullable|integer',
            'img.*.in_hero' => 'nullable|boolean',
            'img.*.in_gallery' => 'nullable|boolean',
        ])['img'] ?? [];

        foreach ($rows as $id => $row) {
            GalleryImage::whereKey($id)->update([
                'caption_hu' => $row['caption_hu'] ?? null,
                'caption_ro' => $row['caption_ro'] ?? null,
                'sort' => (int) ($row['sort'] ?? 0),
                'in_hero' => ! empty($row['in_hero']),
                'in_gallery' => ! empty($row['in_gallery']),
            ]);
        }

        return back()->with('status', 'Mentve.');
    }

    public function destroy(GalleryImage $image)
    {
        foreach ([$image->file, $image->thumb, \App\Support\ImageTool::mobilePath($image->file)] as $rel) {
            $abs = public_path($rel);
            if (str_starts_with($rel, 'uploads/') && is_file($abs)) @unlink($abs);
        }
        $image->delete();

        return back()->with('status', 'Kép törölve.');
    }
}
