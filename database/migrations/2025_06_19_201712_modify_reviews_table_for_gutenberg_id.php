<?php

// database/migrations/xxxx_xx_xx_xxxxxx_modify_reviews_table_for_gutenberg_id.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Remove the old foreign key if it exists
            // $table->dropForeign(['book_id']);
            // $table->dropColumn('book_id');

            // Add the new column for the Gutenberg book ID
            $table->unsignedBigInteger('gutenberg_book_id')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('gutenberg_book_id');

            // If you want to revert, you would add the old book_id column back
            // $table->foreignId('book_id')->constrained()->onDelete('cascade');
        });
    }
};
