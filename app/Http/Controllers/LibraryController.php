<?php

namespace App\Http\Controllers;

use App\Models\FavoriteBook;
use App\Services\GutendexService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    /**
     * Display the user's library of favorite books efficiently.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $perPage = 12;

        // 1. **PERFORMANCE FIX:** Paginate the IDs directly from your database first.
        //    This is extremely fast and avoids fetching all books at once.
        $favoriteBookIdsPaginator = FavoriteBook::where('user_id', $user->id)
            ->latest() // Order by most recently added
            ->paginate($perPage);

        // 2. Fetch the full book details ONLY for the paginated IDs.
        $books = [];
        foreach ($favoriteBookIdsPaginator->items() as $favorite) {
            $bookData = $this->gutendexService->getBookById($favorite->gutenberg_book_id);
            if ($bookData) {
                $books[] = $bookData;
            }
        }

        // 3. Create a new paginator instance with the fetched book details,
        //    while reusing the pagination data (total, current page, etc.) from the original query.
        $paginatedBooks = new LengthAwarePaginator(
            $books,
            $favoriteBookIdsPaginator->total(),
            $perPage,
            $favoriteBookIdsPaginator->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('library.index', ['favoriteBooks' => $paginatedBooks]);
    }

    /**
     * Add a book to the user's library (handles AJAX requests).
     */
    public function add(Request $request)
    {
        // **AJAX FIX:** Validate the incoming JSON data.
        $validated = $request->validate(['gutenberg_book_id' => 'required|integer']);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->favoriteBooks()->syncWithoutDetaching([$validated['gutenberg_book_id']]);

        // **AJAX FIX:** Return a JSON response for the fetch API.
        return response()->json([
            'status' => 'success',
            'message' => 'The book was added to your library!',
        ]);
    }

    /**
     * Remove a book from the user's library (handles AJAX requests).
     */
    public function remove(Request $request)
    {
        // **AJAX FIX:** Validate the incoming JSON data.
        $validated = $request->validate(['gutenberg_book_id' => 'required|integer']);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->favoriteBooks()->detach($validated['gutenberg_book_id']);

        // **AJAX FIX:** Return a JSON response for the fetch API.
        return response()->json([
            'status' => 'success',
            'message' => 'The book was removed from your library.',
        ]);
    }
}
