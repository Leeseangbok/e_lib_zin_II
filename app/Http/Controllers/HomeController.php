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

    /**
     * Show the application dashboard/welcome page.
     * This now fetches curated lists of books directly from the Gutendex API.
     */
    public function index()
    {
        // Define the topics for your featured collections.
        // These are just search terms for the Gutendex API.
        $featuredTopics = [
            'Adventure',
            'Mystery',
            'Science Fiction',
            'Fantasy',
            'Horror',
            'Romance',
        ];

        $collections = [];

        // Use caching to avoid hitting the API on every page load, which is much faster.
        // Cache for 2 hours (120 minutes).
        $collections = Cache::remember('homepage_collections', 120, function () use ($featuredTopics) {
            $fetchedCollections = [];

            // Fetch a general "Popular" list first.
            $fetchedCollections['Popular'] = $this->gutendexService->getBooks(1, null, 12)['results'] ?? [];

            // Fetch books for each of the featured topics.
            foreach ($featuredTopics as $topic) {
                // We'll fetch 6 books for each topic.
                $fetchedCollections[$topic] = $this->gutendexService->getBooks(1, strtolower($topic), 6)['results'] ?? [];
            }
            return $fetchedCollections;
        });

        return view('welcome', compact('collections'));
    }
}
