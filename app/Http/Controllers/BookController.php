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
        $book->title = Str::lower($request->input('title'));
        $book->author = Str::lower($request->input('author'));
        $book->year = $request->input('year');
        $book->category_id = $request->input('category_id');
    
        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $pdfName = Str::slug($request->input('title')) . '.' . $pdf->getClientOriginalExtension();
            $pdfPath = $pdf->storeAs('data', $pdfName, 'public');
            $book->pdf_url = $pdfPath;
    
            // Correct storage paths
            $pdfFullPath = storage_path('app/public/' . $pdfPath);
            $outputDir = storage_path('app/public/cover');
    
            // Log the paths
            \Log::info("PDF Full Path: " . $pdfFullPath);
            \Log::info("Output Directory: " . $outputDir);
    
            // Construct the command
            $pythonScript = base_path('app/python/extract_pdf_cover.py');
            $command = "python3 \"$pythonScript\" \"$pdfFullPath\" \"$outputDir\"";
            
            // Execute the command and capture the output and return value
            $output = [];
            $return_var = 0;
            exec($command, $output, $return_var);
    
            // Log the command and its output
            \Log::info("Command: $command");
            \Log::info("Command Output: " . implode("\n", $output));
            \Log::info("Return Value: $return_var");
    
            if ($return_var !== 0) {
                \Log::error("Python script error: " . implode("\n", $output));
                return redirect()->route('buku')->with('error', 'Failed to generate cover image.');
            }
        }
    
        $book->save();
    
        return redirect()->route('buku')->with('success', 'Book added successfully.');
    }
    
    
    
    
    public function update(Book $book, StoreBookRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['title'] = Str::lower($validatedData['title']);
        $validatedData['author'] = Str::lower($validatedData['author']);
    
        if ($book->title !== $validatedData['title']) {
            // If the title has changed, update the PDF name accordingly
            $newPdfName = Str::slug($validatedData['title']) . '.' . pathinfo($book->pdf_url, PATHINFO_EXTENSION);
            $newPdfPath = str_replace(basename($book->pdf_url), $newPdfName, $book->pdf_url);
            // Rename the PDF file
            Storage::disk('public')->move($book->pdf_url, $newPdfPath);
            // Update the PDF URL in the database
            $validatedData['pdf_url'] = $newPdfPath;
        }
    
        if ($request->hasFile('pdf')) {
            // Delete the existing PDF file
            if ($book->pdf_url) {
                Storage::disk('public')->delete($book->pdf_url);
            }
            // Upload the new PDF file
            $pdf = $request->file('pdf');
            $pdfName = Str::slug($request->input('title')) . '.' . $pdf->getClientOriginalExtension();
            $pdfPath = $pdf->storeAs('data', $pdfName, 'public');
            $book->pdf_url = $pdfPath;
        }
        unset($validatedData['pdf']);
        $book->update($validatedData);
    
        // Return a response with success message
        return redirect()->route('buku')->with('success', 'Book updated successfully.');
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
