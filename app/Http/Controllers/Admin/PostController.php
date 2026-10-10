<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\ImageTool;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.posts.index', ['posts' => Post::latest()->get()]);
    }

    public function create()
    {
        return view('admin.posts.edit', ['post' => new Post(['published_at' => now()])]);
    }

    public function store(Request $request)
    {
        $post = Post::create($this->data($request));
        return redirect()->route('admin.posts.index')->with('status', 'Bejegyzés létrehozva.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $post->update($this->data($request, $post));
        return redirect()->route('admin.posts.index')->with('status', 'Bejegyzés mentve.');
    }

    public function destroy(Post $post)
    {
        $this->removeImage($post->image);
        $post->delete();
        return redirect()->route('admin.posts.index')->with('status', 'Bejegyzés törölve.');
    }

    private function data(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title_ro' => 'nullable|string|max:255',
            'title_hu' => 'nullable|string|max:255',
            'body_ro' => 'nullable|string',
            'body_hu' => 'nullable|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|max:20480',
        ]);
        if (! $request->boolean('published')) $data['published_at'] = null;
        elseif (empty($data['published_at'])) $data['published_at'] = now();

        unset($data['image']);
        if ($request->hasFile('image')) {
            $img = ImageTool::store($request->file('image'), public_path('uploads/hirek'), 'uploads/hirek/');
            if ($post) $this->removeImage($post->image);
            $data['image'] = $img['file'];
        } elseif ($request->boolean('remove_image') && $post) {
            $this->removeImage($post->image);
            $data['image'] = null;
        }

        return $data;
    }

    private function removeImage(?string $rel): void
    {
        if (! $rel || ! str_starts_with($rel, 'uploads/')) return;
        foreach ([$rel, preg_replace('#/([^/]+)$#', '/kicsi/$1', $rel)] as $r) {
            if (is_file(public_path($r))) @unlink(public_path($r));
        }
    }
}
