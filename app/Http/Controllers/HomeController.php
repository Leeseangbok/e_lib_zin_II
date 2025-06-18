<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // ... existing code for fetching collections ...
        $featuredSlugs = ['horror', 'mystery', 'fantasy', 'romance'];
        $featuredCategories = Category::whereIn('slug', $featuredSlugs)
                                        ->with(['books' => function ($query) {
                                            $query->latest()->take(10);
                                        }])
                                        ->get()
                                        ->keyBy('slug');
        $collections = [
            'Latest' => Book::latest()->take(10)->get(),
            'Popular' => Book::inRandomOrder()->take(10)->get(),
        ];
        foreach ($featuredSlugs as $slug) {
            if (isset($featuredCategories[$slug])) {
                $category = $featuredCategories[$slug];
                $collections[$category->name] = $category->books;
            }
        }
        // END of existing code

        // --- ADD THIS ---
        // Fetch all categories to be used in the filter component
        $categories = Category::orderBy('name')->get();
        // --- END OF ADDITION ---


        // Pass both collections and categories to the view
        return view('welcome', compact('collections', 'categories'));
    }
}
