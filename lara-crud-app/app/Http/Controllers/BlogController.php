<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $blogs = Blog::where('user_id', $request->user()->id)
            ->orderBy('id', 'ASC')
            ->paginate(10);
        return view('blog.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage. |
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "title" => "required|string",
            "description" => "required|string",
            "banner_image" => "required|image",
        ]);

        $data["user_id"] = $request->user()->id;

        if ($request->hasFile("banner_image")) {
            $data["banner_image"] = $request->file("banner_image")->store("blogs", "public");
        }

        Blog::create($data);

        return redirect()->route("blog.index")->with("success", "Blog created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return view('blog.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        return view('blog.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            "title" => "required|string",
            "description" => "required|string",
        ]);

        // Cek apakah ada banner baru yang terupload
        if ($request->hasFile('banner_image')) {
            // Menghapus banner lama
            if ($blog->banner_image) {
                Storage::disk('public')->delete($blog->banner_image);
            }

            // Menyimpan banner baru
            $data['banner_image'] = $request->file('banner_image')->store('blogs', 'public');
        }

        $blog->update($data);

        return redirect()->route('blog.show', $blog)->with('success', 'Blog berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        // Menghapus gambar yang ada di storage
        if ($blog->banner_image) {
            Storage::disk('public')->delete($blog->banner_image);
        }

        $blog->delete();

        return redirect()->route('blog.index')->with('success', 'Blog berhasil dihapus');
    }
}
