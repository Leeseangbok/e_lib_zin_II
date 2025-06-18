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
    protected $signature = 'book:fetch-content {--limit=50 : The number of books to fetch content for.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches and stores the full text for books that are missing it';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Attempting to fetch content for up to {$limit} books...");

        // Find books where text_content is NULL and text_url is not NULL
        $booksToFetch = Book::whereNull('text_content')
                              ->whereNotNull('text_url')
                              ->take($limit)
                              ->get();

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
                $response = Http::get($book->text_url);

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
