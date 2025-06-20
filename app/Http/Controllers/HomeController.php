<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Review;
use App\Services\GutendexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    /**
     * Enrich book data with average ratings and a category name.
     *
     * @param array $books
     * @return array
     */
    private function enrichBookData($books)
    {
        if (empty($books)) {
            return [];
        }

        $bookIds = collect($books)->pluck('id')->all();

        $reviews = Review::whereIn('gutenberg_book_id', $bookIds)
            ->selectRaw('gutenberg_book_id, avg(rating) as average_rating')
            ->groupBy('gutenberg_book_id')
            ->get()
            ->keyBy('gutenberg_book_id');

        return collect($books)->map(function ($book) use ($reviews) {
            $book['average_rating'] = $reviews->get($book['id'])->average_rating ?? 0;
            $book['category_name'] = !empty($book['bookshelves']) ? Str::title(collect($book['bookshelves'])->first()) : 'General';
            return $book;
        })->all();
    }

    /**
     * Display the welcome page with featured book carousels.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch homepage collections
        $homePageCollections = Cache::remember('homepage_collections', now()->addHours(6), function () {
            return [
                [
                    'name' => 'Popular This Week',
                    'books' => $this->gutendexService->getBooks(1, 'popular')['results'] ?? []
                ],
                [
                    'name' => 'New Releases',
                    'books' => $this->gutendexService->getBooks(1, 'new')['results'] ?? []
                ],
                [
                    'name' => 'All-Time Classics',
                    'books' => $this->gutendexService->getBooks(1, 'classics')['results'] ?? []
                ]
            ];
        });

        // Enrich homepage collections with review data
        foreach ($homePageCollections as &$collection) {
            $collection['books'] = $this->enrichBookData($collection['books']);
        }

        // Fetch all categories from the database for the filter list
        $categories = Category::all();

        // Fetch books for the featured carousels, caching the results
        $childrenBooksData = Cache::remember('children_books', now()->addHours(6), function () {
            return $this->gutendexService->getBooks(1, 'popular', 'children');
        });
        $fictionBooksData = Cache::remember('fiction_books', now()->addHours(6), function () {
            return $this->gutendexService->getBooks(1, 'popular', 'fiction');
        });
        $mysteryBooksData = Cache::remember('mystery_books', now()->addHours(6), function () {
            return $this->gutendexService->getBooks(1, 'popular', 'mystery');
        });

        // Pass all data to the view
        return view('welcome', [
            'homePageCollections' => $homePageCollections,
            'categories' => $categories,
            'childrenBooks' => collect($this->enrichBookData($childrenBooksData['results'] ?? [])),
            'fictionBooks' => collect($this->enrichBookData($fictionBooksData['results'] ?? [])),
            'mysteryBooks' => collect($this->enrichBookData($mysteryBooksData['results'] ?? [])),
        ]);
    }
}
