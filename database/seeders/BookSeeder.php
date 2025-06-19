<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\GutendexService;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- CONFIGURATION ---
        $pagesToSeed = 10; // Fetch more books to ensure good distribution
        $featuredSlugs = ['horror', 'mystery', 'fantasy', 'romance'];

        // 1. Get the specific categories we want to feature
        $featuredCategories = Category::whereIn('slug', $featuredSlugs)->get();
        if ($featuredCategories->count() !== count($featuredSlugs)) {
            $this->command->error('One or more featured categories are missing from the database. Please check your CategorySeeder.');
            return;
        }

        // Get all other categories for random assignment
        $otherCategories = Category::whereNotIn('slug', $featuredSlugs)->get();

        $this->command->info("--- Seeding Books from Gutendex API ---");
        $this->command->info("Preparing to fetch {$pagesToSeed} pages...");

        $progressBar = $this->command->getOutput()->createProgressBar($pagesToSeed);
        $progressBar->start();

        $totalBooksAdded = 0;
        $featuredBookCounter = 0;

        for ($page = 1; $page <= $pagesToSeed; $page++) {
            $bookData = $this->gutendexService->getBooks($page);

            if (!$bookData || empty($bookData['results'])) {
                $this->command->warn("\nCould not fetch data for page {$page}. Stopping.");
                break;
            }

            foreach ($bookData['results'] as $bookItem) {
                if (!isset($bookItem['formats']['text/plain; charset=us-ascii']) && !isset($bookItem['formats']['text/plain'])) {
                    continue;
                }

                // 2. Assign categories intelligently
                $categoryId = null;
                // Assign the first books evenly to our featured categories
                if ($featuredBookCounter < ($featuredCategories->count() * 10) && $featuredCategories->count() > 0) { // ensure first 10 books for each category
                    $categoryIndex = $featuredBookCounter % $featuredCategories->count();
                    $categoryId = $featuredCategories[$categoryIndex]->id;
                    $featuredBookCounter++;
                } else if ($otherCategories->isNotEmpty()) {
                    // Assign the rest randomly to other categories
                    $categoryId = $otherCategories->random()->id;
                } else {
                    // Fallback to any category if there are no "other" categories
                    $categoryId = $featuredCategories->random()->id;
                }

                // 3. Create the book
                $created = Book::updateOrCreate(
                    ['id' => $bookItem['id']],
                    [
                        'title' => $bookItem['title'],
                        'author' => $bookItem['authors'][0]['name'] ?? 'Unknown',
                        'description' => "A classic work by " . ($bookItem['authors'][0]['name'] ?? 'Unknown'),
                        'cover_image_url' => $bookItem['formats']['image/jpeg'] ?? null,
                        'text_url' => $bookItem['formats']['text/plain; charset=us-ascii'] ?? $bookItem['formats']['text/plain'] ?? null,
                        'category_id' => $categoryId
                    ]
                );

                if($created->wasRecentlyCreated) {
                    $totalBooksAdded++;
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->info("\n\nBook seeding complete. Added {$totalBooksAdded} new books.");
    }
}
