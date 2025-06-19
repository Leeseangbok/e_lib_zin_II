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
            // This is trying to add a column that already exists
            $table->unsignedBigInteger('gutenberg_book_id')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('gutenberg_book_id');
        });
    }
};
