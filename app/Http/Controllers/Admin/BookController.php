<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class BookController extends Controller
{
    // ... (index, create, store methods remain the same)

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('author', 'like', '%' . $searchTerm . '%')
                  ->orWhere('isbn', 'like', '%' . $searchTerm . '%');
        }

        $books = $query->with('category')->latest()->paginate(10);
        return view('admin.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.books.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'isbn' => 'required|string|unique:books,isbn',
            'published_year' => 'required|integer|min:1000|max:' . date('Y'),
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'book_file' => 'nullable|file|mimes:pdf',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover_image')) {
            $data['cover_image_url'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($request->hasFile('book_file')) {
            $data['text_url'] = $request->file('book_file')->store('books', 'public');
        }

        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Book created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'published_year' => 'required|integer|min:1000|max:' . date('Y'),
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'book_file' => 'nullable|file|mimes:pdf',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('cover_image')) {
            // Delete old cover if it exists
            if ($book->cover_image_url) {
                Storage::disk('public')->delete($book->cover_image_url);
            }
            $data['cover_image_url'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($request->hasFile('book_file')) {
            // Delete old book file if it exists
            if ($book->text_url) {
                Storage::disk('public')->delete($book->text_url);
            }
            $data['text_url'] = $request->file('book_file')->store('books', 'public');
        }

        $book->update($data);

        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        // Delete associated files from storage
        if ($book->cover_image_url) {
            Storage::disk('public')->delete($book->cover_image_url);
        }
        if ($book->text_url) {
            Storage::disk('public')->delete($book->text_url);
        }

        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Book deleted successfully.');
    }
}
