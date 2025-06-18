<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchBookContentCommand extends Command
{
    protected $signature = 'book:fetch-content {--limit=50 : The number of books to fetch content for.}';
    protected $description = 'Fetches and stores the EPUB file for books that are missing it';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Attempting to fetch content for up to {$limit} books...");

        // **MODIFIED**: Find books that have an epub_url but no content yet.
        $booksToFetch = Book::whereNull('text_content')
                              ->whereNotNull('epub_url')
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
                // **MODIFIED**: Fetch from the epub_url.
                $response = Http::get($book->epub_url);

                if ($response->successful()) {
                    // **MODIFIED**: Save the raw binary content of the EPUB file.
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
