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
            // Create a file name with title_author_year using Str::slug to handle spaces
            $title = Str::limit($request->input('title'), 50);
            $pdfName = Str::slug($request->input('title') . '_' . $request->input('author') . '_' . $request->input('year'), '_') . '.' . $pdf->getClientOriginalExtension();
            $pdfPath = $pdf->storeAs('data', $pdfName, 'public');
            $book->pdf_url = $pdfPath;

            // Generate cover image
            $pdfFullPath = storage_path('app/public/' . $pdfPath);
            $outputDir = storage_path('app/public/cover');

            $pythonScript = base_path('app/python/extract_pdf_cover.py');
            $command = "python3 \"$pythonScript\" \"$pdfFullPath\" \"$outputDir\"";

            $output = [];
            $return_var = 0;
            exec($command, $output, $return_var);

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

    $pdfUpdated = false;

    if ($book->title !== $validatedData['title'] || $book->author !== $validatedData['author'] || $book->year !== $validatedData['year']) {
        // If the title, author, or year has changed, update the PDF name accordingly
        $title = Str::limit($validatedData['title'], 50);
        $newPdfName = Str::slug($validatedData['title'] . '_' . $validatedData['author'] . '_' . $validatedData['year'], '_') . '.' . pathinfo($book->pdf_url, PATHINFO_EXTENSION);
        $newPdfPath = str_replace(basename($book->pdf_url), $newPdfName, $book->pdf_url);

        // Rename the PDF file
        Storage::disk('public')->move($book->pdf_url, $newPdfPath);
        // Update the PDF URL in the database
        $validatedData['pdf_url'] = $newPdfPath;

        // Rename the cover image
        $coverImagePath = str_replace('data/', 'cover/', $book->pdf_url);
        $newCoverImagePath = str_replace(basename($book->pdf_url), basename($newPdfPath, '.' . pathinfo($newPdfPath, PATHINFO_EXTENSION)) . '.jpg', $coverImagePath);
        Storage::disk('public')->move($coverImagePath, $newCoverImagePath);

        $pdfUpdated = true;
    }

    if ($request->hasFile('pdf')) {
        // Delete the existing PDF file
        if ($book->pdf_url && !$pdfUpdated) {
            Storage::disk('public')->delete($book->pdf_url);
        }
        // Delete the existing cover image
        $coverImagePath = str_replace('data/', 'cover/', $book->pdf_url);
        if (Storage::disk('public')->exists($coverImagePath) && !$pdfUpdated) {
            Storage::disk('public')->delete($coverImagePath);
        }

        // Upload the new PDF file
        $pdf = $request->file('pdf');
        $pdfName = Str::slug($validatedData['title'] . '_' . $validatedData['author'] . '_' . $validatedData['year'], '_') . '.' . $pdf->getClientOriginalExtension();
        $pdfPath = $pdf->storeAs('data', $pdfName, 'public');
        $validatedData['pdf_url'] = $pdfPath;

        // Generate the new cover image
        $pdfFullPath = storage_path('app/public/' . $pdfPath);
        $outputDir = storage_path('app/public/cover');

        $pythonScript = base_path('app/python/extract_pdf_cover.py');
        $command = "python3 \"$pythonScript\" \"$pdfFullPath\" \"$outputDir\"";

        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            \Log::error("Python script error: " . implode("\n", $output));
            return redirect()->route('buku')->with('error', 'Failed to generate cover image.');
        }
    }

    unset($validatedData['pdf']);
    $book->update($validatedData);

    return redirect()->route('buku')->with('success', 'Book updated successfully.');
}


    public function delete($id)
    {
        // Retrieve the book
        $book = Book::findOrFail($id);

        // Delete the associated PDF file (if it exists)
        if ($book->pdf_url) {
            Storage::disk('public')->delete($book->pdf_url);

            // Construct the cover image path
            $coverImagePath = str_replace('data/', 'cover/', $book->pdf_url);
            $coverImagePath = pathinfo($coverImagePath, PATHINFO_DIRNAME) . '/' . pathinfo($coverImagePath, PATHINFO_FILENAME) . '.png';

            // Delete the associated cover image
            if (Storage::disk('public')->exists($coverImagePath)) {
                Storage::disk('public')->delete($coverImagePath);
            }
        }

        // Delete the book record from the database
        $book->delete();

        // Return a response with success message
        return redirect()->route('buku')->with('success', 'Book deleted successfully.');
    }
}
