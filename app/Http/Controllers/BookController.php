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
        $oldPdfPath = $book->pdf_url;
        $oldCoverImagePath = 'cover/' . pathinfo($oldPdfPath, PATHINFO_FILENAME) . '.png';
        
        // Check if title, author, or year has changed
        if ($book->title !== $validatedData['title'] || $book->author !== $validatedData['author'] || $book->year !== $validatedData['year']) {
            $newPdfName = Str::slug($validatedData['title'] . '_' . $validatedData['author'] . '_' . $validatedData['year'], '_') . '.' . pathinfo($oldPdfPath, PATHINFO_EXTENSION);
            $newPdfPath = 'data/' . $newPdfName;
            
            // Rename the PDF file
            if ($oldPdfPath && Storage::disk('public')->exists($oldPdfPath)) {
                Storage::disk('public')->move($oldPdfPath, $newPdfPath);
                $validatedData['pdf_url'] = $newPdfPath;
    
                // Rename the cover image
                $newCoverImagePath = 'cover/' . pathinfo($newPdfName, PATHINFO_FILENAME) . '.png';
                if (Storage::disk('public')->exists($oldCoverImagePath)) {
                    Storage::disk('public')->move($oldCoverImagePath, $newCoverImagePath);
                }
                $pdfUpdated = true;
            }
        }
    
        if ($request->hasFile('pdf')) {
            if (!$pdfUpdated && $oldPdfPath) {
                Storage::disk('public')->delete($oldPdfPath);
                Storage::disk('public')->delete($oldCoverImagePath);
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
    
        // Explicitly set the attributes and save the book
        $book->fill($validatedData);
        if (isset($validatedData['pdf_url'])) {
            $book->pdf_url = $validatedData['pdf_url'];
        }
        $book->save();
    
        
        return redirect()->route('buku')->with('success', 'Book updated successfully.');
    }
    

    public function delete($id)
    {
        $book = Book::findOrFail($id);
        if ($book->pdf_url) {
            Storage::disk('public')->delete($book->pdf_url);
            $coverImagePath = str_replace('data/', 'cover/', $book->pdf_url);
            $coverImagePath = pathinfo($coverImagePath, PATHINFO_DIRNAME) . '/' . pathinfo($coverImagePath, PATHINFO_FILENAME) . '.jpg';
            if (Storage::disk('public')->exists($coverImagePath)) {
                Storage::disk('public')->delete($coverImagePath);
            }
        }
        $book->delete();
        return redirect()->route('buku')->with('success', 'Book deleted successfully.');
    }

    public function showAllBooks(Request $request)
    {
        $perPage = (int) $request->get('perPage', 10);
        $search = $request->get('search', '');
        
        $query = Book::with('category');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('year', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $books = $query->orderBy('id', 'desc')->paginate($perPage);
        $categories = Category::all();
        
        // Render the partial view into HTML
        $html = view('partial.book_table', compact('books', 'categories'))->render();
        
        if ($request->ajax()) {
            // Return JSON response with the HTML view
            return response()->json([
                'currentPage' => $books->currentPage(),
                'lastPage' => $books->lastPage(),
                'url' => route('buku'), // Base URL for pagination links
                'html' => $html
            ]);
        }
        
        // Return the regular Blade view with data for non-AJAX requests
        return view('admin.buku', compact('books', 'categories', 'perPage', 'search', 'html'));
    }
    
    
    
    
      
}
