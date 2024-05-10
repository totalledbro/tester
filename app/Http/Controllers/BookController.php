<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        $book->category_id = $request->input('category_id');

        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $pdfName = Str::slug($request->input('title')) . '.' . $pdf->getClientOriginalExtension();
            $pdfPath = $pdf->storeAs('data', $pdfName, 'public');
            $book->pdf_url = $pdfPath;
        }
    
        $book->save();
            // Return a response with success message
        return redirect()->route('buku');
    }
    
    public function delete($id)
    {
        // Retrieve the book
        $book = Book::findOrFail($id);

        // Delete the associated PDF file (if it exists)
        if ($book->pdf_url) {
            Storage::disk('public')->delete($book->pdf_url);
        }

        // Delete the book record from the database
        $book->delete();

        // Return a response with success message
        return redirect()->route('buku')->with('success', 'Book deleted successfully.');
    }
}
