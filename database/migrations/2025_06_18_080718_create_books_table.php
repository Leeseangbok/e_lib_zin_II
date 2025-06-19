<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->text('description');
            $table->string('isbn')->unique()->nullable(); // Add ISBN column
            $table->string('language')->default('English'); // Add language column
            $table->year('published_year');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('cover_image_path')->nullable();
            $table->string('book_file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
