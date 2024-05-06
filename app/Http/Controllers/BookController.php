<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return view('index', compact('books'));
    }

    public function add(StoreBookRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['title'] = Str::lower($validatedData['title']);
        $validatedData['author'] = Str::lower($validatedData['author']);

        if ($request->hasFile('pdf')) {
            // Get the contents of the PDF file
            $pdfContent = file_get_contents($request->file('pdf'));
            $validatedData['pdf'] = $pdfContent;
        }

        Book::create($validatedData);

        // Redirect to the index page
        return redirect()->route('buku.index');
    }
    
  
    public function update(Book $book, StoreBookRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['title'] = Str::lower($validatedData['title']);
        $validatedData['author'] = Str::lower($validatedData['author']);

        if ($request->hasFile('pdf')) {
            // Get the contents of the PDF file
            $pdfContent = file_get_contents($request->file('pdf'));
            $validatedData['pdf'] = $pdfContent;
        }

        $book->update($validatedData);

        // Redirect to the index page
        return redirect()->route('buku.index');
    }

 
    public function delete(Book $book)
    {
        $book->delete();

        // Redirect to the index page
        return redirect()->route('buku.index');
    }
}
