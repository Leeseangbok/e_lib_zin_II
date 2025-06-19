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
        Schema::table('books', function (Blueprint $table) {
            $table->string('ebook_no')->nullable()->after('id');
            $table->string('original_publication')->nullable()->after('publication_date');
            $table->text('credits')->nullable()->after('description');
            $table->string('copyright_status')->nullable()->after('credits');
            $table->integer('downloads')->default(0)->after('copyright_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'ebook_no',
                'original_publication',
                'credits',
                'copyright_status',
                'downloads',
            ]);
        });
    }
};
