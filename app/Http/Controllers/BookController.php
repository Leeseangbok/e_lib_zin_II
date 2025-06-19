<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class BookController extends Controller
{
    /**
     * Display a paginated list of books.
     */
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

    /**
     * Display a single book's details.
     */
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
