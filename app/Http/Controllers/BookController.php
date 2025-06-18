<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('author', 'like', '%' . $searchTerm . '%');
            });
        }

        $categoryName = $request->has('category')
            ? \App\Models\Category::where('slug', $request->category)->value('name')
            : null;

        $books = $query->orderBy('title')->paginate(12);

        return view('books.index', compact('books', 'categoryName'));
    }

    public function show(Book $book)
    {
        $book->load('reviews.user');
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->inRandomOrder()
            ->take(8)
            ->get();
        return view('books.show', compact('book', 'relatedBooks'));
    }

    // --- MODIFICATION START ---
    public function read(Book $book)
    {
        // If an EPUB URL exists, show the EPUB view.
        if ($book->epub_url) {
            return view('books.read_epub', [
                'book' => $book,
                'epubUrl' => $book->epub_url,
            ]);
        }

        // --- Fallback to plain text reader if no EPUB is available. ---
        $chapters = [];
        $bookContent = $book->text_content;

        if (empty($bookContent)) {
            $chapters[] = ['title' => 'Error', 'content' => '<p>The text for this book has not been downloaded yet or it has no plain-text version available.</p>'];
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
    // --- MODIFICATION END ---

    private function extractBookContent(string $rawText): string
    {
        $startMarker = '*** START OF THE PROJECT GUTENBERG EBOOK';
        $endMarker = '*** END OF THE PROJECT GUTENBERG EBOOK';

        $startIndex = strpos($rawText, $startMarker);
        if ($startIndex !== false) {
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

    private function formatChapterContent(array $lines): string
    {
        $content = implode("\n", $lines);
        $content = trim($content);
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $paragraphs = preg_split('/(\r\n|\n|\r){2,}/', $content);
        return collect($paragraphs)
            ->map(fn ($p) => trim($p))
            ->filter()
            ->map(fn ($p) => '<p class="mb-6">' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '</p>')
            ->implode('');
    }
}
