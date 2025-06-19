<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_favorite_books_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('gutenberg_book_id'); // To store the book ID from the Gutendex API
            $table->timestamps();

            $table->unique(['user_id', 'gutenberg_book_id']); // Prevent duplicate entries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_books');
    }
};
