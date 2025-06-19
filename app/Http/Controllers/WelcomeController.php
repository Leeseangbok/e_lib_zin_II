<?php

namespace App\Http\Controllers;

use App\Services\GutendexService;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     * This will now fetch the most popular books to display on the welcome page.
     */
    public function __invoke(GutendexService $gutendexService)
    {
        // The default Gutendex API call sorts by popularity.
        // Let's get the top books.
        $popularBooksData = $gutendexService->getBooks(1, null);

        // Get the first book as the main featured book
        $featuredBook = $popularBooksData['results'][0] ?? null;

        // Get the next few books for a "popular" list
        $popularBooks = array_slice($popularBooksData['results'] ?? [], 1, 4);

        return view('welcome', [
            'featuredBook' => $featuredBook,
            'popularBooks' => $popularBooks
        ]);
    }
}
