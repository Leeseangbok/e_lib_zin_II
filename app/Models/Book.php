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
        'cover_image_url',
        'text_url',
    ];

    /**
     * Get the category that the book belongs to.
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'book_user');
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
        return $this->text_url ? Storage::url($this->text_url) : null;
    }
}
