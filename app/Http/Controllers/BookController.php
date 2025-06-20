<?php

namespace App\Http\Controllers;

use App\Models\FavoriteBook;
use App\Models\Review;
use App\Services\GutendexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    private function enrichBookData(array $books): array
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
            return $book;
        })->all();
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $topic = $request->input('topic');

        $booksData = $this->gutendexService->getBooks($page, $search, $topic);
        $booksData['results'] = $this->enrichBookData($booksData['results'] ?? []);

        if (!empty($booksData['results'])) {
            foreach ($booksData['results'] as &$book) {
                if ($topic) {
                    $book['category_name'] = Str::title(str_replace('-', ' ', $topic));
                } else {
                    $book['category_name'] = !empty($book['bookshelves']) ? Str::title(collect($book['bookshelves'])->first()) : 'General';
                }
            }
        }

        $categoryName = $topic ? Str::title(str_replace('-', ' ', $topic)) : null;

        return view('books.index', [
            'books' => $booksData,
            'categoryName' => $topic ? ucfirst($topic) : ($search ? 'Search Results' : 'Browse All Books')
        ]);
    }

    public function show($id)
    {
        $book = $this->gutendexService->getBookById($id);

        if (!$book) {
            abort(404, 'Book not found.');
        }

        $isFavorite = Auth::check() ? FavoriteBook::where('user_id', Auth::id())->where('gutenberg_book_id', $id)->exists() : false;

        $reviews = Review::where('gutenberg_book_id', $id)->with('user')->latest()->get();
        $averageRating = $reviews->avg('rating');

        // --- Start of new related books logic ---
        $relatedBooksData = [];

        if (!empty($book['bookshelves'])) {
            $topic = strtolower($book['bookshelves'][0]);
            $relatedBooksData = $this->gutendexService->getBooks(1, null, $topic);
        }

        if (empty($relatedBooksData['results'])) {
            $authorName = $book['authors'][0]['name'] ?? null;
            if ($authorName) {
                $relatedBooksData = $this->gutendexService->getBooks(1, $authorName, null);
            } else {
                $relatedBooksData = ['results' => []];
            }
        }

        $allRelated = $relatedBooksData['results'] ?? [];
        $filteredRelated = array_filter($allRelated, fn ($related) => $related['id'] != $id);
        $relatedBooks = array_slice($this->enrichBookData($filteredRelated), 0, 5);
        // --- End of new related books logic ---

        return view('books.show', compact('book', 'relatedBooks', 'isFavorite', 'reviews', 'averageRating'));
    }

    /*
     * The toggleFavorite() method was removed because this functionality is now
     * handled by the add() and remove() methods in LibraryController,
     * which are called via AJAX from the book details page.
     */

    public function read($id)
    {
        $book = $this->gutendexService->getBookById($id);
        $textContentUrl = $book['text_url'] ?? null;

        if (!$textContentUrl) {
            Log::error("No 'text_url' found for book ID: {$id}", ['book_data' => $book]);
            abort(404, 'No readable text version found for this book.');
        }

        try {
            $rawText = cache()->remember("book.content.{$id}", 1440, function () use ($textContentUrl, $id) {
                $response = Http::get($textContentUrl);
                if ($response->failed()) {
                    Log::error("Failed to fetch book content for ID: {$id} from URL: {$textContentUrl}", ['status' => $response->status()]);
                    return null;
                }
                return $response->body();
            });

            if ($rawText === null) {
                abort(500, 'Could not retrieve book content from the source.');
            }
        } catch (\Exception $e) {
            Log::error("Exception while fetching book content for ID: {$id}", ['message' => $e->getMessage()]);
            abort(500, 'An error occurred while trying to fetch the book content.');
        }

        $chapters = $this->splitIntoChapters($this->extractBookContent($rawText));
        return view('books.read', ['book' => $book, 'chapters' => $chapters]);
    }

    private function extractBookContent(string $rawText): string
    {
        $startMarker = '*** START OF THE PROJECT GUTENBERG EBOOK';
        $endMarker = '*** END OF THE PROJECT GUTENBERG EBOOK';

        $startIndex = strpos($rawText, $startMarker);
        if ($startIndex !== false) {
            $startIndex = strpos($rawText, "\n", $startIndex) ?: $startIndex;
        } else {
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
        $chapterRegex = '/^\s*(chapter|prologue|epilogue|part|section|book|letter)\s*([IVXLCDM\d\s\.-]+|[a-zA-Z\d\s\.-]+)\.?\s*$/imU';

        $lines = explode("\n", $bookContent);
        $currentChapterTitle = 'Introduction';
        $currentChapterLines = [];

        foreach ($lines as $line) {
            if (preg_match($chapterRegex, trim($line), $matches)) {
                if (!empty(trim(implode("\n", $currentChapterLines)))) {
                    $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
                }
                $currentChapterTitle = Str::title(trim($matches[0]));
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
        $paragraphs = preg_split('/(\r\n|\n){2,}/', $content);
        $html = '';
        foreach ($paragraphs as $p) {
            $trimmedP = trim($p);
            if (!empty($trimmedP)) {
                $html .= '<p class="mb-4">' . nl2br(e($trimmedP)) . '</p>';
            }
        }
        return $html;
    }
}
