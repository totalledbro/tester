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
        dd($request->all());
        // Validate the request data
        $validatedData = $request->validated();
        $validatedData['title'] = Str::lower($validatedData['title']);
        $validatedData['author'] = Str::lower($validatedData['author']);
        
        // Save the file to public/data folder
        $pdfUrl = $request->file('pdf')->store('public/data');

        // Create a new book instance
        $book = Book::create($validatedData + ['pdf_url' => $pdfUrl]);

        // Return a response or redirect to the book's page
        return response(
        );

        // Redirect to the index page
        return redirect()->route('buku.index')->with('success', 'Book uploaded successfully');
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
