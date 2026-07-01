<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::withCount('authors')->latest()->get();
        return view('admin.countries.index', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:countries,name',
            'code' => 'nullable|string|max:10',
        ]);
        Country::create($request->only(['name', 'code']));
        return back()->with('success', 'Country added.');
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:countries,name,' . $country->id,
            'code' => 'nullable|string|max:10',
        ]);
        $country->update($request->only(['name', 'code']));
        return back()->with('success', 'Country updated.');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return back()->with('success', 'Country deleted.');
    }
}