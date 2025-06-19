<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified category and its books.
     */
    public function show(Category $category)
    {
        // Load the books related to this category, paginated
        $books = $category->books()->paginate(12);
        return view('categories.show', compact('category', 'books'));
    }
}
