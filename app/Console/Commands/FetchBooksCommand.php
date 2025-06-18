<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GutendexService; // We will reuse our service
use App\Models\Book;
use App\Models\Category;

class FetchBooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:fetch {--pages=5 : The number of pages to fetch from Gutendex.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch books from the Gutendex API and store them in the database';

    protected $gutendexService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GutendexService $gutendexService)
    {
        parent::__construct();
        $this->gutendexService = $gutendexService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pagesToFetch = $this->option('pages');
        $this->info("Fetching {$pagesToFetch} pages of books from Gutendex...");

        // Get all categories once to avoid querying in a loop
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->error('No categories found. Please seed the categories table first by running: php artisan db:seed --class=CategorySeeder');
            return 1; // Indicate failure
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
                break; // Stop if the API returns no more results
            }

            foreach ($bookData['results'] as $bookItem) {
                // Skip books without a plain text version for reading
                if (!isset($bookItem['formats']['text/plain; charset=us-ascii']) && !isset($bookItem['formats']['text/plain'])) {
                    continue;
                }

                $created = Book::updateOrCreate(
                    ['id' => $bookItem['id']], // Unique identifier to prevent duplicates
                    [
                        'title' => $bookItem['title'],
                        'author' => $bookItem['authors'][0]['name'] ?? 'Unknown',
                        'description' => "A classic work by " . ($bookItem['authors'][0]['name'] ?? 'Unknown') . ".",
                        'cover_image_url' => $bookItem['formats']['image/jpeg'] ?? null,
                        'text_url' => $bookItem['formats']['text/plain; charset=us-ascii'] ?? $bookItem['formats']['text/plain'] ?? null,
                        'category_id' => $categories->random()->id // Assign a random category
                    ]
                );

                if ($created->wasRecentlyCreated) {
                    $totalBooksAdded++;
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nCommand finished. Added {$totalBooksAdded} new books to the database.");

        return 0; // Indicate success
    }

    /**
     * A helper function to find the latest page we fetched from
     * to avoid re-fetching the same initial pages.
     * This is a simple implementation assuming book IDs are incremental.
     */
    private function getHighestPageFetched(): int
    {
        $latestBookId = Book::max('id') ?? 0;
        // The API returns 32 results per page.
        return (int) floor($latestBookId / 32);
    }
}
