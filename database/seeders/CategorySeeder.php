<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Fiction',
            'Adventure',
            'Mystery',
            'Science Fiction',
            'Fantasy',
            'Horror',
            'Historical Fiction',
            'Romance',
            'Thriller',
            'Non-Fiction',
            'Biography',
            'Self-Help',
            'Health & Wellness',
            'Travel',
            'Cooking',
            'Technology',
            'Education',
            'Philosophy',
            'Religion',
            'Politics',
            'Business',
            'Art',
            'Poetry',
            'Children\'s',
            'Young Adult',
            'Comics',
            'Classics',
            'Short Stories',
            'Drama'
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name)
            ]);
        }
    }
}
