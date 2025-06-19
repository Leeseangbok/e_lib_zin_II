<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\FavoriteBook;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // <-- MODIFIED: Added 'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user has the 'admin' role.
     */
    public function isAdmin(): bool // <-- MODIFIED: Added this method
    {
        return $this->role === 'admin';
    }

    /**
     * The books that the user has marked as favorites (in their library).
     */

    public function favoriteBooks(): BelongsToMany
    {
        // Change from hasMany to belongsToMany
        return $this->belongsToMany(
            Book::class,          // The related model
            'favorite_books',     // The name of the pivot table
            'user_id',            // The foreign key in the pivot table for the User model
            'gutenberg_book_id'   // The foreign key in the pivot table for the Book model
        )->withTimestamps(); // Optional: keeps the created_at/updated_at on the pivot table updated
    }
    /**
     * The reviews that the user has written.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
