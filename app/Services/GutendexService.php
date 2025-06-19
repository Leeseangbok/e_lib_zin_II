<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GutendexService
{
    protected $baseUrl = 'https://gutendex.com/books';

    public function getBooks(int $page = 1, ?string $search = null)
    {
        $query = ['page' => $page];
        if ($search) {
            $query['search'] = urlencode($search); // Important: URL-encode search terms
        }

        $response = Http::get($this->baseUrl, $query);

        // If the request failed, return a default "empty" response.
        if (!$response->successful()) {
            return ['count' => 0, 'next' => null, 'previous' => null, 'results' => []];
        }

        return $response->json();
    }
    /**
     * Fetches a single book by its Gutenberg ID and maps the API data
     * to the structure of our local Book model.
     */
    public function getBookById(int $id): ?array // Return value can be null
    {
        $response = Http::get("{$this->baseUrl}/{$id}");

        // If a book is not found (404) or the server errors, abort.
        if (!$response->successful()) {
            abort(404, 'The requested book could not be found.');
        }

        $data = $response->json();

        // --- Helper function to find a specific text format URL ---
        $findFormat = fn($formats, $mimeType) => $formats[$mimeType] ?? null;

        // --- Helper to generate a simple description ---
        $generateDescription = function ($subjects) {
            if (empty($subjects)) {
                return 'No description available.';
            }
            $descriptiveSubjects = array_filter($subjects, function ($subject) {
                return !Str::contains(strtolower($subject), ['fiction', 'literature']);
            });
            $description = "This book explores themes of " . Str::lower(implode(', ', array_slice($descriptiveSubjects, 0, 4))) . ".";
            return Str::limit($description, 250);
        };

        return [
            'id' => $data['id'],
            'title' => $data['title'] ?? 'Untitled',
            //... all the other fields
            'author' => $data['authors'][0]['name'] ?? 'Unknown Author',
            'authors' => $data['authors'] ?? [],
            'subjects' => $data['subjects'] ?? [],
            'bookshelves' => $data['bookshelves'] ?? [],
            'languages' => $data['languages'] ?? [],
            'copyright_status' => isset($data['copyright']) ? ($data['copyright'] ? 'Yes' : 'No') : 'Unknown',
            'downloads' => $data['download_count'] ?? 0,
            'cover_image_url' => $findFormat($data['formats'], 'image/jpeg'),
            'text_url' => $findFormat($data['formats'], 'text/plain; charset=us-ascii')
                ?? $findFormat($data['formats'], 'text/plain; charset=utf-8')
                ?? $findFormat($data['formats'], 'text/plain'),
            'description' => $generateDescription($data['subjects'] ?? []),
            'credits' => !empty($data['agents']) ? implode(', ', array_column($data['agents'], 'person')) : 'Not specified',
            'release_date' => null,
            'original_publication' => $data['authors'][0]['birth_year'] ?? 'N/A',
        ];
    }
}
