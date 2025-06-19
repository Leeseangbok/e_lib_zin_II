<?php

namespace App\Http\Controllers;

use App\Services\GutendexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Import Http facade
use Illuminate\Support\Str;

class BookController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    /**
     * Display a listing of books from the API. (Unchanged)
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $bookData = $this->gutendexService->getBooks($page, $search);

        return view('books.index', [
            'books' => $bookData, // Pass the whole response
            'categoryName' => $search ? 'Search Results' : 'Browse All Books'
        ]);
    }

    /**
     * Display the specified book fetched directly from the API.
     */
    public function show($id)
    {
        // Fetch the main book's data directly from the API.
        $book = $this->gutendexService->getBookById($id);

        // Fetch related books based on the author's name.
        $authorName = $book['authors'][0]['name'] ?? null;
        $relatedBooksData = $authorName ? $this->gutendexService->getBooks(1, $authorName) : ['results' => []];

        // Filter out the current book from the related list and take 5
        $relatedBooks = array_filter($relatedBooksData['results'], function($related) use ($id) {
            return $related['id'] != $id;
        });
        $relatedBooks = array_slice($relatedBooks, 0, 5);


        return view('books.show', compact('book', 'relatedBooks'));
    }

    /**
     * Display the book's content for reading. Fetches content on-the-fly.
     */
public function read($id)
{
    // 1. Get book metadata (this is fast and can also be cached)
    $book = $this->gutendexService->getBookById($id);
    $textContentUrl = $book['text_url'];

    if (!$textContentUrl) {
        abort(404, 'No readable text version found for this book.');
    }

    // 2. Cache the book content to avoid re-downloading
    // The cache key is unique to the book's ID. Cache for 1 day (1440 minutes).
    $rawText = cache()->remember("book.content.{$id}", 1440, function () use ($textContentUrl) {
        // This closure only runs if the item is NOT in the cache.
        return Http::get($textContentUrl)->body();
    });

    // 3. Process the text (now using the cached version)
    $chapters = $this->splitIntoChapters($this->extractBookContent($rawText));

    return view('books.read', [
        'book' => $book,
        'chapters' => $chapters,
    ]);
}
    // --- Private Helper Methods for Text Processing (Unchanged) ---

    private function extractBookContent(string $rawText): string
    {
        $startMarker = '*** START OF THE PROJECT GUTENBERG EBOOK';
        $endMarker = '*** END OF THE PROJECT GUTENBERG EBOOK';

        // Find the start of the actual content
        $startIndex = strpos($rawText, $startMarker);
        if ($startIndex !== false) {
            $startIndex = strpos($rawText, "\n", $startIndex) ?: $startIndex;
        } else {
            $startIndex = 0; // Fallback if marker isn't found
        }

        // Find the end of the actual content
        $endIndex = strrpos($rawText, $endMarker); // Use last occurrence
        if ($endIndex === false) {
            $endIndex = strlen($rawText); // Fallback
        }

        return trim(substr($rawText, $startIndex, $endIndex - $startIndex));
    }

    private function splitIntoChapters(string $bookContent): array
    {
        $chapters = [];
        // Improved regex to catch more chapter formats
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

        // Add the last chapter
        if (!empty(trim(implode("\n", $currentChapterLines)))) {
            $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
        }

        // If no chapters were found, return the whole book as a single chapter
        if (empty($chapters)) {
            $chapters[] = ['title' => 'Full Text', 'content' => $this->formatChapterContent(explode("\n", $bookContent))];
        }

        return $chapters;
    }

    private function formatChapterContent(array $lines): string
    {
        // Wrap paragraphs in <p> tags for better formatting
        $content = trim(implode("\n", $lines));
        $paragraphs = explode("\n\n", $content); // Split by double newline
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
