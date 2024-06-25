<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('index', compact('categories'));
    }

    public function add(StoreCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['name'] = Str::lower($validatedData['name']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::slug($validatedData['name']) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('kategori', $imageName, 'public');
            $validatedData['image_url'] = $imagePath;
        }

        $category = Category::create($validatedData);
        return redirect()->route('datakategori')->with('success', 'Category added successfully.');
    }

    public function update(Category $category, StoreCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['name'] = Str::lower($validatedData['name']);

        if ($request->hasFile('image')) {
            if ($category->image_url) {
                Storage::disk('public')->delete($category->image_url);
            }
            $image = $request->file('image');
            $imageName = Str::slug($validatedData['name']) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('kategori', $imageName, 'public');
            $validatedData['image_url'] = $imagePath;
        }

        $category->update($validatedData);
        return redirect()->route('datakategori')->with('success', 'Category updated successfully.');
    }

    public function delete(Category $category)
    {
        if ($category->image_url) {
            Storage::disk('public')->delete($category->image_url);
        }
        $category->delete();
        return redirect()->route('datakategori')->with('success', 'Category deleted successfully.');
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $books = $category->books()->with('category')->get(); 

        return view('auth.isikategori', compact('category', 'books'));
    }
}
