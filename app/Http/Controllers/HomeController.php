<?php

namespace App\Http\Controllers;

use App\Services\GutendexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    public function index()
    {
        // Existing code for homePageCollections
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

        // --- New code for category carousel ---
        $categories = [
            ['name' => 'Fiction', 'slug' => 'fiction'],
            ['name' => 'Fantasy', 'slug' => 'fantasy'],
            ['name' => 'Adventure', 'slug' => 'adventure'],
            ['name' => 'Horror', 'slug' => 'horror'],
            ['name' => 'Science Fiction', 'slug' => 'science-fiction'],
        ];

        $categoryBooks = [];
        foreach ($categories as $category) {
            $cacheKey = 'category_books_' . $category['slug'];
            // Cache for 6 hours
            $categoryBooks[$category['slug']] = Cache::remember($cacheKey, now()->addHours(6), function () use ($category) {
                // Fetch the first page of books for the category
                return $this->gutendexService->getBooks(1, null, $category['slug'])['results'] ?? [];
            });
        }
        // --- End of new code ---

        return view('welcome', [
            'homePageCollections' => $homePageCollections,
            'categories' => $categories, // Pass categories to the view
            'categoryBooks' => $categoryBooks, // Pass category books to the view
        ]);
    }
}
