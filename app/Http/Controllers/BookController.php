<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function index(Request $request)
    {
        // Start with a clean query builder instance
        $query = Book::query();

        // Filter by Category if a category slug is present in the URL
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Add text search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('author', 'like', '%' . $searchTerm . '%');
            });
        }


        // Get the current category name for the heading, if it exists
        $categoryName = $request->has('category')
            ? \App\Models\Category::where('slug', $request->category)->value('name')
            : null;

        $books = $query->orderBy('title')->paginate(12);

        return view('books.index', compact('books', 'categoryName'));
    }


    // In BookController.php
    public function show(Book $book)
    {
        // Eager load the reviews and the user associated with each review
        $book->load('reviews.user');

        // Fetch related books from the same category, excluding the current book
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id) // Exclude the book itself
            ->inRandomOrder()
            ->take(8) // Get up to 6 related books
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function read(Book $book)
    {
        if ($book->epub_url) {
            return view('books.read_epub', [
                'book' => $book,
                'epubUrl' => $book->epub_url,
            ]);
        }

        $chapters = [];
        $bookContent = $book->text_content;

        if (empty($bookContent)) {
            $chapters[] = ['title' => 'Error', 'content' => '<p>The text for this book has not been downloaded yet. Please ask the site administrator to fetch the content.</p>'];
        } else {
            $bookContent = $this->extractBookContent($bookContent);
            $chapterRegex = '/^\s*(chapter|prologue|epilogue|part)\s*([IVXLCDM\d]+|[a-zA-Z\s]+)\.?\s*$/im';
            $lines = explode("\n", $bookContent);
            $currentChapterTitle = 'Introduction';
            $currentChapterLines = [];

            foreach ($lines as $line) {
                $trimmedLine = trim($line);
                if (preg_match($chapterRegex, $trimmedLine)) {
                    if (!empty($currentChapterLines)) {
                        $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
                    }
                    $currentChapterTitle = $trimmedLine;
                    $currentChapterLines = [];
                } else {
                    $currentChapterLines[] = $line;
                }
            }

            if (!empty($currentChapterLines)) {
                $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
            }

            if (empty($chapters)) {
                $chapters[] = ['title' => 'Full Text', 'content' => $this->formatChapterContent(explode("\n", $bookContent))];
            }
        }

        return view('books.read', [
            'book' => $book,
            'chapters' => $chapters,
        ]);
    }
    /**
     * Extracts the core text of a book, removing Gutenberg headers/footers.
     */
    private function extractBookContent(string $rawText): string
    {
        $startMarker = '*** START OF THE PROJECT GUTENBERG EBOOK';
        $endMarker = '*** END OF THE PROJECT GUTENBERG EBOOK';

        $startIndex = strpos($rawText, $startMarker);
        if ($startIndex !== false) {
            // Find the end of the start marker line
            $startIndex = strpos($rawText, "\n", $startIndex) ?: $startIndex;
        } else {
            $startIndex = 0;
        }

        $endIndex = strpos($rawText, $endMarker);
        if ($endIndex === false) {
            $endIndex = strlen($rawText);
        }

        return substr($rawText, $startIndex, $endIndex - $startIndex);
    }

    /**
     * Helper function to format an array of lines into HTML paragraphs.
     */
    private function formatChapterContent(array $lines): string
    {
        $content = implode("\n", $lines);
        $content = trim($content);
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

        $paragraphs = preg_split('/(\r\n|\n|\r){2,}/', $content);

        return collect($paragraphs)
            ->map(fn($p) => trim($p))
            ->filter()
            ->map(fn($p) => '<p class="mb-6">' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '</p>')
            ->implode('');
    }
}
