<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // Corresponds to Gutenberg ID
            $table->string('title');
            $table->string('author')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->text('text_url')->nullable();
            $table->string('language')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
