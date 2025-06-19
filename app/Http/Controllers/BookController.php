<?php

namespace App\Http\Controllers;

use App\Services\GutendexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteBook;
use App\Models\Review;
use Illuminate\Support\Facades\Log; // Import Log facade

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
        $topic = $request->input('topic');

        $bookData = $this->gutendexService->getBooks($page, $search, $topic);

        return view('books.index', [
            'books' => $bookData,
            'categoryName' => $topic ? ucfirst($topic) : ($search ? 'Search Results' : 'Browse All Books')
        ]);
    }

    public function show($id)
    {
        $book = $this->gutendexService->getBookById($id);

        if (!$book) {
            abort(404, 'Book not found.');
        }

        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = FavoriteBook::where('user_id', Auth::id())
                ->where('gutenberg_book_id', $id)
                ->exists();
        }

        $reviews = Review::where('gutenberg_book_id', $id)
            ->with('user')
            ->latest()
            ->get();

        $authorName = $book['authors'][0]['name'] ?? null;
        $relatedBooksData = $authorName ? $this->gutendexService->getBooks(1, $authorName) : ['results' => []];
        $relatedBooks = array_filter($relatedBooksData['results'], fn($related) => $related['id'] != $id);
        $relatedBooks = array_slice($relatedBooks, 0, 5);

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
// In app/Http/Controllers/BookController.php

public function read($id)
{
    $book = $this->gutendexService->getBookById($id);

    // Get the text URL directly from the 'text_url' key provided by your service.
    $textContentUrl = $book['text_url'] ?? null;

    if (!$textContentUrl) {
        // If the text_url is missing for any reason, log it and show an error.
        Log::error("No 'text_url' found for book ID: {$id}", ['book_data' => $book]);
        abort(404, 'No readable text version found for this book.');
    }

    try {
        // Use the text_url to fetch the content.
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

    // The rest of the logic to split into chapters remains the same.
    $chapters = $this->splitIntoChapters($this->extractBookContent($rawText));

    return view('books.read', [
        'book' => $book,
        'chapters' => $chapters,
    ]);
}


    // --- Private Helper Methods (unchanged from previous version) ---

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
