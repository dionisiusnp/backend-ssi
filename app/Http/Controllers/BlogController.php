<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $query = Blog::query();

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $query->orderBy('created_at', 'desc');

        $blogs = $query->paginate($perPage);

        $blogs->getCollection()->transform(function ($blog) {
            $blog->image_url = url($blog->getFirstMediaUrl('images'));
            return $blog;
        });

        return response()->json($blogs);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $blog = Blog::create($request->all());

        if ($request->hasFile('file')) {
            $blog->addMediaFromRequest('file')->toMediaCollection('images');
        }

        return response()->json($blog, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return $blog;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $blog->update($request->all());

        if ($request->hasFile('file')) {
            if ($blog->hasMedia('images')) {
                $blog->clearMediaCollection('images');
            }
            $blog->addMediaFromRequest('file')->toMediaCollection('images');
        }

        return response()->json($blog, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json(null, 204);
    }
}
