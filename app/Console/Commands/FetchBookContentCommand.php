<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchBookContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // --- MODIFICATION: Added --force option ---
    protected $signature = 'book:fetch-content {--limit=50 : The number of books to fetch content for.} {--force : Whether to re-download content even if it exists.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches and stores the full text for books that are missing it, with an option to force re-download';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $force = $this->option('force');
        $this->info("Attempting to fetch content for up to {$limit} books...");
        if ($force) {
            $this->warn('The --force flag is enabled. Existing content will be overwritten.');
        }

        // --- MODIFICATION: Query changes based on --force flag ---
        $query = Book::query()->whereNotNull('text_url');

        if (!$force) {
            // If not forcing, only get books with no content.
            $query->whereNull('text_content');
        }

        $booksToFetch = $query->take($limit)->get();
        // --- END MODIFICATION ---

        if ($booksToFetch->isEmpty()) {
            $this->info('No books found that need their content fetched. All set!');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($booksToFetch->count());
        $progressBar->start();

        $fetchedCount = 0;
        $failedCount = 0;

        foreach ($booksToFetch as $book) {
            try {
                // --- MODIFICATION: Added timeout and retry logic for robustness ---
                $response = Http::timeout(30)      // Wait a maximum of 30 seconds for a response
                                  ->retry(3, 200)  // Retry 3 times with a 200ms delay between attempts
                                  ->get($book->text_url);
                // --- END MODIFICATION ---

                if ($response->successful()) {
                    $book->text_content = $response->body();
                    $book->save();
                    $fetchedCount++;
                } else {
                    $this->warn("\nFailed to fetch content for \"{$book->title}\" (ID: {$book->id}). Status: {$response->status()}");
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("\nAn exception occurred for \"{$book->title}\" (ID: {$book->id}): {$e->getMessage()}");
                Log::error("Failed to fetch content for book ID {$book->id}: {$e->getMessage()}");
                $failedCount++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\n\nContent fetching complete.");
        $this->info("Successfully fetched: {$fetchedCount} books.");
        if ($failedCount > 0) {
            $this->warn("Failed to fetch: {$failedCount} books.");
        }

        return Command::SUCCESS;
    }
}
