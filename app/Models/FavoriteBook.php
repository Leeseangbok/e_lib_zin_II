<?php

// app/Models/FavoriteBook.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class FavoriteBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gutenberg_book_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
