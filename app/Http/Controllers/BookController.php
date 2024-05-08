<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Str;

class BookController extends Controller
{

    public function index()
    {
        $books = Book::with('category')->get();
        return view('index', compact('books'));
    }

    public function add(StoreBookRequest $request)
    {
        $book = new Book();
        // Validate the request data
        $book->title = $request->input('title');
        $book->author = $request->input('author');
        $book->year = $request->input('year');
        $book->pdf_url = $request->input('pdf');
        $book->category_id = $request->input('category_id');
        $book->save();
            // Return a response with success message
        return redirect()->route('buku');
    }
    
}
