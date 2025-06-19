<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
        'author', // Note: Storing the primary author's name for simplicity.
        'authors', // Storing the full author data array.
        'subjects',
        'bookshelves',
        'languages',
        'copyright_status',
        'cover_image_url',
        'text_url',
        'downloads',
        'description', // A generated description.
        'credits',
        'release_date',
        'original_publication'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'authors' => 'array',
        'subjects' => 'array',
        'bookshelves' => 'array',
        'release_date' => 'date',
    ];

    /**
     * We're using the Gutenberg ID as our primary key, so it's not auto-incrementing.
     */
    public $incrementing = false;
    protected $keyType = 'integer';


    // Relationships (unchanged)
    public function category()
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
}
