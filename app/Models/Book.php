<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // We are using guarded instead of fillable to allow all attributes
    // to be mass-assignable, since we control the data coming from the API.
    // This is simpler than listing every single column.
    protected $guarded = [];

    // ... all of your relationships like category(), reviews(), etc. ...

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
