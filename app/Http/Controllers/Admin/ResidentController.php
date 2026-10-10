<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\ResidentPhoto;
use App\Models\ResidentUpdate;
use App\Support\ImageTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResidentController extends Controller
{
    public function index()
    {
        return view('admin.residents.index', ['residents' => Resident::withCount(['users', 'photos', 'updates'])->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.residents.form', ['resident' => new Resident(['active' => true])]);
    }

    public function store(Request $request)
    {
        $resident = Resident::create($this->data($request));
        return redirect()->route('admin.residents.show', $resident)->with('status', 'Lakó létrehozva.');
    }

    public function show(Resident $resident)
    {
        return view('admin.residents.show', ['resident' => $resident->load(['users', 'updates.photos', 'photos'])]);
    }

    public function edit(Resident $resident)
    {
        return view('admin.residents.form', compact('resident'));
    }

    public function update(Request $request, Resident $resident)
    {
        $resident->update($this->data($request));
        return redirect()->route('admin.residents.show', $resident)->with('status', 'Mentve.');
    }

    public function destroy(Resident $resident)
    {
        Storage::disk('local')->deleteDirectory("residents/{$resident->id}");
        $resident->delete();
        return redirect()->route('admin.residents.index')->with('status', 'Lakó és minden hozzá tartozó adat törölve.');
    }

    /** Hír + fotók a hozzátartozóknak. */
    public function storeUpdate(Request $request, Resident $resident)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:10000',
            'photos' => 'array|max:30',
            'photos.*' => 'image|max:20480',
            'caption' => 'nullable|string|max:255',
        ]);
        abort_if(blank($data['title'] ?? null) && blank($data['body'] ?? null) && ! $request->hasFile('photos'), 422, 'Üres bejegyzés.');

        $update = $resident->updates()->create([
            'author_id' => $request->user()->id,
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
        ]);

        $dir = Storage::disk('local')->path("residents/{$resident->id}");
        foreach ($request->file('photos', []) as $file) {
            $img = ImageTool::store($file, $dir, "residents/{$resident->id}/");
            $resident->photos()->create([
                'resident_update_id' => $update->id,
                'path' => $img['file'],
                'thumb' => $img['thumb'],
                'caption' => $data['caption'] ?? null,
            ]);
        }

        return back()->with('status', 'Hír közzétéve a hozzátartozóknak.');
    }

    public function destroyUpdate(ResidentUpdate $update)
    {
        foreach ($update->photos as $photo) $this->deletePhoto($photo);
        $update->delete();
        return back()->with('status', 'Hír törölve.');
    }

    public function destroyPhoto(ResidentPhoto $photo)
    {
        $this->deletePhoto($photo);
        return back()->with('status', 'Fotó törölve.');
    }

    private function deletePhoto(ResidentPhoto $photo): void
    {
        Storage::disk('local')->delete([$photo->path, $photo->thumb]);
        $photo->delete();
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'room' => 'nullable|string|max:40',
            'moved_in_at' => 'nullable|date',
        ]);
        $data['active'] = $request->boolean('active');
        return $data;
    }
}
