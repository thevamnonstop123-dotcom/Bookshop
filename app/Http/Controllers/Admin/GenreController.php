<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('authors')->latest()->get();
        return view('admin.genres.index', compact('genres'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:genres,name']);
        Genre::create(['name' => $request->name]);
        return back()->with('success', 'Genre added.');
    }

    public function update(Request $request, Genre $genre)
    {
        $request->validate(['name' => 'required|string|max:100|unique:genres,name,' . $genre->id]);
        $genre->update(['name' => $request->name]);
        return back()->with('success', 'Genre updated.');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return back()->with('success', 'Genre deleted.');
    }
}