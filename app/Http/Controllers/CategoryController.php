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
        return redirect()->route('datakategori')->with('success');
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
        return redirect()->route('datakategori')->with('success');
    }
// app/Http/Controllers/CategoryController.php

    public function checkCategoryUsage($id)
    {
        $category = Category::findOrFail($id);

        if ($category->books()->count() > 0) {
            return response()->json(['canDelete' => false]);
        }

        return response()->json(['canDelete' => true]);
    }

    public function delete(Category $category)
    {
        // Check if there are any books associated with this category
        if ($category->books()->count() > 0) {
            // Redirect back with an error message
            return redirect()->route('datakategori')->with('error');
        }
    
        // Delete the category's image if it exists
        if ($category->image_url) {
            Storage::disk('public')->delete($category->image_url);
        }
    
        // Delete the category
        $category->delete();
    
        // Redirect back with a success message
        return redirect()->route('datakategori')->with('success');
    }
    

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $books = $category->books()->with('category')->get(); 

        return view('auth.isikategori', compact('category', 'books'));
    }
}
