<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate_Database_Eloquent_Relations_BelongsTo;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'isbn',
        'language',
        'published_year',
        'category_id',
        'cover_image_path',
        'book_file_path',
    ];

    /**
     * Get the category that the book belongs to.
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the full URL for the book's cover image.
     */
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image_path) {
            return Storage::url($this->cover_image_path);
        }
        // Return a default image if no cover is set
        return 'https://via.placeholder.com/150';
    }

    /**
     * Get the full URL for the book's file.
     */
    public function getBookFileUrlAttribute()
    {
        return $this->book_file_path ? Storage::url($this->book_file_path) : null;
    }
}
