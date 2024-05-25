<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view('index', compact('categories'));
    }

    public function add(StoreCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['name'] = Str::lower($validatedData['name']);
        $category = Category::create($validatedData);
        return redirect()->route('datakategori');
    }
    

    public function update(Category $category, StoreCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['name'] = Str::lower($validatedData['name']);
        $category->update($validatedData);
        return redirect()->route('kategori');
    }

 
    public function delete(Category $category)
    {
        $category->delete();
        return redirect()->route('kategori');
    }
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $books = $category->books; // Assuming you have a relationship set up

        return view('auth.isikategori', compact('category', 'books'));
    }
}
