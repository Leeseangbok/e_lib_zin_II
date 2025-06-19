<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Services\GutendexService;
use App\Jobs\ProcessBookContent;
use Illuminate\Pagination\LengthAwarePaginator;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class BookController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    /**
     * Display a listing of the resource from the API.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');

        // Fetch book list directly from the Gutenberg API
        $bookData = $this->gutendexService->getBooks($page, $search);

        $totalResults = $bookData['count'] ?? 0;
        $perPage = 32; // Gutendex API returns 32 items per page
        $items = $bookData['results'] ?? [];

        // Create a Paginator so your Blade views can use ->hasPages() and ->links()
        $books = new LengthAwarePaginator(
            $items,
            $totalResults,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('books.index', [
            'books' => $books,
            'categoryName' => $search ? 'Search Results' : 'Browse All Books'
        ]);
    }

    /**
     * Display the specified resource.
     * Fetches from API on first visit, then loads from local DB.
     */
    public function show($id)
    {
        // On first visit, fetch from API and create the record in our database.
        Book::firstOrCreate(
            ['id' => $id],
            $this->gutendexService->getBookById($id)
        );

        // Now, load the book from our local database. This is faster.
        $book = Book::findOrFail($id);

        // Fetch related books from API based on the author.
        $relatedBooksData = $this->gutendexService->getBooks(1, $book->author);
        $relatedBooks = array_slice($relatedBooksData['results'] ?? [], 0, 5);

        return view('books.show', compact('book', 'relatedBooks'));
    }

    /**
     * Display the book's content for reading.
     * Downloads content in the background to prevent timeouts.
     */
    public function read($id)
    {
        // Make sure the book metadata is in our database.
        Book::firstOrCreate(
            ['id' => $id],
            $this->gutendexService->getBookById($id)
        );

        $book = Book::findOrFail($id);

        // If the full text content has not been downloaded yet...
        if (empty($book->text_content)) {
            // ...dispatch a background job to download it from the Gutenberg API.
            ProcessBookContent::dispatch($book);
            // ...and show the user a temporary loading page.
            return view('books.loading', ['book' => $book]);
        }

        // If content IS ready, process it into chapters and display the reader page.
        $chapters = $this->splitIntoChapters($this->extractBookContent($book->text_content));

        return view('books.read', [
            'book' => $book,
            'chapters' => $chapters,
        ]);
    }

    // --- Private Helper Methods for Text Processing ---

    private function extractBookContent(string $rawText): string
    {
        $startMarker = '*** START OF THE PROJECT GUTENBERG EBOOK';
        $endMarker = '*** END OF THE PROJECT GUTENBERG EBOOK';

        $startIndex = strpos($rawText, $startMarker);
        $startIndex = ($startIndex !== false) ? strpos($rawText, "\n", $startIndex) ?: $startIndex : 0;

        $endIndex = strpos($rawText, $endMarker);
        $endIndex = ($endIndex !== false) ? $endIndex : strlen($rawText);

        return substr($rawText, $startIndex, $endIndex - $startIndex);
    }

    private function splitIntoChapters(string $bookContent): array
    {
        $chapters = [];
        $chapterRegex = '/^\s*(chapter|prologue|epilogue|part|section)\s*([IVXLCDM\d]+|[a-zA-Z\s]+)\.?\s*$/im';
        $lines = explode("\n", $bookContent);
        $currentChapterTitle = 'Introduction';
        $currentChapterLines = [];

        foreach ($lines as $line) {
            if (preg_match($chapterRegex, trim($line))) {
                if (!empty(trim(implode("\n", $currentChapterLines)))) {
                    $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
                }
                $currentChapterTitle = trim($line);
                $currentChapterLines = [];
            } else {
                $currentChapterLines[] = $line;
            }
        }

        if (!empty(trim(implode("\n", $currentChapterLines)))) {
            $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
        }

        // Fallback for books with no clear chapter markers
        if (empty($chapters)) {
            $chapters[] = ['title' => 'Full Text', 'content' => $this->formatChapterContent(explode("\n", $bookContent))];
        }

        return $chapters;
    }

    private function formatChapterContent(array $lines): string
    {
        $content = trim(implode("\n", $lines));
        $html = nl2br(e($content)); // Convert newlines to <br> and escape HTML

        // You can add more advanced formatting here if needed later

        return $html;
    }
}
