<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Book; // Assuming you brought back the Book model

class ProcessBookContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $book;

    /**
     * Create a new job instance.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find the plain text URL
        if (isset($this->book->formats['text/plain; charset=us-ascii'])) {
            $textContentUrl = $this->book->formats['text/plain; charset=us-ascii'];

            // Download the content
            $response = Http::get($textContentUrl);

            if ($response->successful()) {
                // Save the content to the book in the database
                $this->book->text_content = $response->body();
                $this->book->save();
            }
        }
    }
}
