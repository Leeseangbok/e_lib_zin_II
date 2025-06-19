<?php

namespace App\Http\Controllers;

use App\Services\GutendexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteBook;
use App\Models\Review; // <-- Import the Review model

class BookController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $topic = $request->input('topic'); // Get topic from request

        $bookData = $this->gutendexService->getBooks($page, $search, $topic); // Pass topic to service

        return view('books.index', [
            'books' => $bookData,
            'categoryName' => $topic ? ucfirst($topic) : ($search ? 'Search Results' : 'Browse All Books')
        ]);
    }

    /**
     * Display the specified book with its reviews and favorite status.
     */
   public function show($id)
    {
        // 1. Fetch book data from the API
        $book = $this->gutendexService->getBookById($id);

        if (!$book) {
            abort(404, 'Book not found.');
        }

        // 2. Check favorite status directly against the pivot table
        $isFavorite = false;
        if (Auth::check()) {
            // This is the corrected logic: Query the pivot table directly.
            $isFavorite = FavoriteBook::where('user_id', Auth::id())
                                      ->where('gutenberg_book_id', $id)
                                      ->exists();
        }

        // 3. Fetch reviews for this book from your database
        $reviews = Review::where('gutenberg_book_id', $id)
                         ->with('user') // Eager load user data
                         ->latest()     // Show newest first
                         ->get();

        // 4. Fetch related books (no changes here)
        $authorName = $book['authors'][0]['name'] ?? null;
        $relatedBooksData = $authorName ? $this->gutendexService->getBooks(1, $authorName) : ['results' => []];
        $relatedBooks = array_filter($relatedBooksData['results'], fn($related) => $related['id'] != $id);
        $relatedBooks = array_slice($relatedBooks, 0, 5);

        // 5. Pass all data to the view
        return view('books.show', compact('book', 'relatedBooks', 'isFavorite', 'reviews'));
    }


    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'gutenberg_book_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $gutenbergBookId = $request->gutenberg_book_id;

        $favorite = $user->favoriteBooks()->where('gutenberg_book_id', $gutenbergBookId)->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Book removed from your library.');
        } else {
            FavoriteBook::create([
                'user_id' => $user->id,
                'gutenberg_book_id' => $gutenbergBookId,
            ]);
            return back()->with('success', 'Book added to your library.');
        }
    }

    public function read($id)
    {
        $book = $this->gutendexService->getBookById($id);
        $textContentUrl = $book['formats']['text/plain; charset=us-ascii'] ?? $book['formats']['text/plain'] ?? null;

        if (!$textContentUrl) {
            abort(404, 'No readable text version found for this book.');
        }

        $rawText = cache()->remember("book.content.{$id}", 1440, function () use ($textContentUrl) {
            return Http::get($textContentUrl)->body();
        });

        $chapters = $this->splitIntoChapters($this->extractBookContent($rawText));

        return view('books.read', [
            'book' => $book,
            'chapters' => $chapters,
        ]);
    }


    // --- Private Helper Methods ---

    private function extractBookContent(string $rawText): string
    {
        $startMarker = '*** START OF THE PROJECT GUTENBERG EBOOK';
        $endMarker = '*** END OF THE PROJECT GUTENBERG EBOOK';

        $startIndex = strpos($rawText, $startMarker);
        if ($startIndex !== false) {
            $startIndex = strpos($rawText, "\n", $startIndex) ?: $startIndex;
        } else {
            // A more robust fallback for books missing the START marker
            $altStartMarker = '*** START OF THIS PROJECT GUTENBERG EBOOK';
            $startIndex = strpos($rawText, $altStartMarker);
            if ($startIndex !== false) {
                $startIndex = strpos($rawText, "\n", $startIndex) ?: $startIndex;
            } else {
                $startIndex = 0;
            }
        }

        $endIndex = strrpos($rawText, $endMarker);
        if ($endIndex === false) {
            $endIndex = strrpos($rawText, '*** END OF THE PROJECT GUTENBERG EBOOK');
        }

        if ($endIndex === false) {
            $endIndex = strlen($rawText);
        }

        return trim(substr($rawText, $startIndex, $endIndex - $startIndex));
    }

    private function splitIntoChapters(string $bookContent): array
    {
        $chapters = [];
        $chapterRegex = '/^\s*(chapter|prologue|epilogue|part|section|book)\s*([IVXLCDM\d]+|[a-zA-Z\s]+)\.?\s*$/im';
        $lines = explode("\n", $bookContent);
        $currentChapterTitle = 'Introduction';
        $currentChapterLines = [];

        foreach ($lines as $line) {
            if (preg_match($chapterRegex, trim($line))) {
                if (!empty(trim(implode("\n", $currentChapterLines)))) {
                    $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
                }
                $currentChapterTitle = Str::title(trim($line));
                $currentChapterLines = [];
            } else {
                $currentChapterLines[] = $line;
            }
        }

        if (!empty(trim(implode("\n", $currentChapterLines)))) {
            $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
        }

        if (empty($chapters)) {
            $chapters[] = ['title' => 'Full Text', 'content' => $this->formatChapterContent(explode("\n", $bookContent))];
        }

        return $chapters;
    }

    private function formatChapterContent(array $lines): string
    {
        $content = trim(implode("\n", $lines));
        $paragraphs = explode("\n\n", $content);
        $html = '';
        foreach ($paragraphs as $p) {
            $trimmedP = trim($p);
            if (!empty($trimmedP)) {
                $html .= '<p>' . nl2br(e($trimmedP)) . '</p>';
            }
        }
        return $html;
    }
}
