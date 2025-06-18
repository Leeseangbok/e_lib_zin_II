<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GutendexService
{
    protected $baseUrl = 'https://gutendex.com/books';

    /**
     * Fetch a list of books by page number.
     */
    public function getBooks(int $page = 1, ?string $search = null)
    {
        $query = ['page' => $page];
        if ($search) {
            $query['search'] = $search;
        }

        return $this->fetchFromApi($this->baseUrl, $query);
    }

    /**
     * Fetch a list of books from a full URL.
     */
    public function getBooksByUrl(string $url)
    {
        return $this->fetchFromApi($url);
    }

    /**
     * Centralized API call logic.
     */
    protected function fetchFromApi(string $url, array $query = [])
    {
        $response = Http::get($url, $query);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Fetch a single book by its ID.
     */
    public function getBookById(int $id)
    {
        $response = Http::get("{$this->baseUrl}/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
