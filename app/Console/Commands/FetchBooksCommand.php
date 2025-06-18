<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GutendexService;
use App\Models\Book;
use App\Models\Category;

class FetchBooksCommand extends Command
{
    protected $signature = 'books:fetch {--pages=5 : The number of pages to fetch from Gutendex.}';
    protected $description = 'Fetch books from the Gutendex API and store them in the database';
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        parent::__construct();
        $this->gutendexService = $gutendexService;
    }

    public function handle()
    {
        $pagesToFetch = $this->option('pages');
        $this->info("Fetching {$pagesToFetch} pages of books from Gutendex...");

        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->error('No categories found. Please seed the categories table first by running: php artisan db:seed --class=CategorySeeder');
            return 1;
        }

        $totalBooksAdded = 0;
        $startPage = $this->getHighestPageFetched() + 1;
        $endPage = $startPage + $pagesToFetch - 1;

        $this->info("Starting from page {$startPage} up to page {$endPage}.");

        $progressBar = $this->output->createProgressBar($pagesToFetch);
        $progressBar->start();

        for ($page = $startPage; $page <= $endPage; $page++) {
            $bookData = $this->gutendexService->getBooks($page);

            if (!$bookData || empty($bookData['results'])) {
                $this->warn("\nCould not fetch data for page {$page} or no more books available. Stopping.");
                break;
            }

            foreach ($bookData['results'] as $bookItem) {
                // --- MODIFICATION START ---
                // Prioritize EPUB format, but also get plain text as a fallback.
                $epubUrl = $bookItem['formats']['application/epub+zip'] ?? null;
                $plainTextUrl = $bookItem['formats']['text/plain; charset=us-ascii'] ?? $bookItem['formats']['text/plain'] ?? null;

                // Skip if no usable format is found.
                if (!$epubUrl && !$plainTextUrl) {
                    continue;
                }

                $created = Book::updateOrCreate(
                    ['id' => $bookItem['id']],
                    [
                        'title' => $bookItem['title'],
                        'author' => $bookItem['authors'][0]['name'] ?? 'Unknown',
                        'description' => "A classic work by " . ($bookItem['authors'][0]['name'] ?? 'Unknown') . ".",
                        'cover_image_url' => $bookItem['formats']['image/jpeg'] ?? null,
                        'epub_url' => $epubUrl, // Save the new EPUB URL
                        'text_url' => $plainTextUrl, // Save the plain text URL as a fallback
                        'category_id' => $categories->random()->id
                    ]
                );
                // --- MODIFICATION END ---

                if ($created->wasRecentlyCreated) {
                    $totalBooksAdded++;
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nCommand finished. Added {$totalBooksAdded} new books to the database.");
        return 0;
    }

    private function getHighestPageFetched(): int
    {
        $latestBookId = Book::max('id') ?? 0;
        return (int) floor($latestBookId / 32);
    }
}
