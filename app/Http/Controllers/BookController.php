<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use App\Models\Category;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 public function index(Request $request)
    {
        $query = Book::query();
        $categoryName = null; // <-- MODIFIED: Initialize $categoryName to null

        // Check if a category filter is present in the request
        if ($request->has('category')) {
            // Find the category by its slug
            $category = Category::where('slug', $request->category)->firstOrFail();
            // Filter books by the found category's ID
            $query->where('category_id', $category->id);
            // Set the category name for the view
            $categoryName = $category->name;
        }

        // Get the paginated list of books
        $books = $query->latest()->paginate(12);

        // Pass the books and the category name (which is now always defined) to the view
        return view('books.index', compact('books', 'categoryName'));
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        // --- Google Books API Integration ---
        $searchQuery = $book->isbn ? 'isbn:' . $book->isbn : 'intitle:' . urlencode($book->title);
        $googleResponse = Http::get("https://www.googleapis.com/books/v1/volumes?q={$searchQuery}&maxResults=1");

        if ($googleResponse->successful() && $googleResponse->json('totalItems') > 0) {
            $volumeInfo = $googleResponse->json('items')[0]['volumeInfo'];

            // Clean and set the publication date
            if (empty($book->publication_date) && isset($volumeInfo['publishedDate'])) {
                // Remove any non-numeric or non-dash characters to prevent parsing errors
                $cleanedDate = preg_replace('/[^\d\-]/', '', $volumeInfo['publishedDate']);
                $book->publication_date = $cleanedDate;
            }

            if (empty($book->publisher) && isset($volumeInfo['publisher'])) {
                $book->publisher = $volumeInfo['publisher'];
            }
        }

        // --- Gutendex (Project Gutenberg) API Integration ---
        $gutenbergData = [];
        $gutendexResponse = Http::get("https://gutendex.com/books?search=" . urlencode($book->title));

        if ($gutendexResponse->successful() && $gutendexResponse->json('count') > 0) {
            $gutenbergBook = $gutendexResponse->json('results')[0];
            $gutenbergData['subjects'] = $gutenbergBook['subjects'] ?? [];
            $gutenbergData['bookshelves'] = $gutenbergBook['bookshelves'] ?? [];
        }

        // --- Eager Loading and Related Books ---
        $book->load([
            'category:id,name,slug',
            'reviews' => function ($query) {
                $query->with('user:id,name')->latest();
            }
        ]);

        $relatedBooks = Book::query()
            ->select('id', 'title', 'author', 'cover_image_url')
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks', 'gutenbergData'));
    }
    /**
     * Display the book's content for reading.
     */
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
            $chapters[] = [
                'title' => 'Content Not Available',
                'content' => '<p>The text for this book is not available in plain-text format.</p>'
            ];
        } else {
            $cleanedContent = $this->extractBookContent($bookContent);
            $chapters = $this->splitIntoChapters($cleanedContent);
        }

        return view('books.read', [
            'book' => $book,
            'chapters' => $chapters,
        ]);
    }

    /**
     * Remove Gutenberg headers and footers.
     */
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

    /**
     * Split book content into chapters.
     */
    private function splitIntoChapters(string $bookContent): array
    {
        $chapters = [];
        $chapterRegex = '/^\s*(chapter|prologue|epilogue|part|section)\s*([IVXLCDM\d]+|[a-zA-Z\s]+)\.?\s*$/im';
        $lines = explode("\n", $bookContent);
        $currentChapterTitle = 'Introduction';
        $currentChapterLines = [];

        foreach ($lines as $line) {
            if (preg_match($chapterRegex, trim($line))) {
                if (!empty($currentChapterLines)) {
                    $chapters[] = ['title' => $currentChapterTitle, 'content' => $this->formatChapterContent($currentChapterLines)];
                }
                $currentChapterTitle = trim($line);
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

        return $chapters;
    }

    /**
     * Format chapter content to HTML with advanced typography using GFM.
     */
    private function formatChapterContent(array $lines): string
    {
        $content = implode("\n", $lines);
        $content = trim($content);

        // Handle scene breaks (* * *)
        $content = preg_replace('/^\s*(\*\s*){3,}\s*$/m', '<hr class="my-8 border-gray-400 border-dashed">', $content);

        // Configure Markdown environment for GitHub Flavored Markdown
        $environment = new Environment([
            'default_attributes' => [
                \League\CommonMark\Extension\CommonMark\Node\Block\Heading::class => ['class' => 'text-2xl font-bold mt-8 mb-4'],
                \League\CommonMark\Node\Block\Paragraph::class => ['class' => 'mb-4 leading-relaxed'],
                \League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote::class => ['class' => 'border-l-4 border-gray-500 pl-4 italic my-4'],
                \League\CommonMark\Extension\CommonMark\Node\Block\ListItem::class => ['class' => 'mb-2'],
                \League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis::class => ['class' => 'italic'],
                \League\CommonMark\Extension\CommonMark\Node\Inline\Strong::class => ['class' => 'font-bold'],
            ],
            'allow_unsafe_links' => false,
            'gfm' => [
                'strikethrough_class' => 'line-through',
            ],
        ]);

        $environment->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
        $environment->addExtension(new DefaultAttributesExtension());

        // Use the GithubFlavoredMarkdownConverter which includes smart punctuation
        $converter = new GithubFlavoredMarkdownConverter([], $environment);

        $html = $converter->convert($content);

        return mb_convert_encoding((string) $html, 'UTF-8', 'UTF-8');
    }
}
