<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('rating');
            $table->text('content');
            $table->timestamps();
            $table->unique(['user_id', 'book_id']); // A user can only review a book once
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
