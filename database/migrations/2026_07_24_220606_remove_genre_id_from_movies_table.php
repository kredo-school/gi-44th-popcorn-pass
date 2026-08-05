<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        if (Schema::hasColumn('movies', 'genre_id')) {

            Schema::table('movies', function (Blueprint $table) {

                $table->dropForeign(['genre_id']);

                $table->dropColumn('genre_id');
            });
        }
    }


    public function down(): void
    {
        if (!Schema::hasColumn('movies', 'genre_id')) {
            Schema::table('movies', function (Blueprint $table) {
                $table->uuid('genre_id')->nullable();

                $table->foreign('genre_id')
                    ->references('id')
                    ->on('genres')
                    ->cascadeOnDelete();
            });
        }
    }
};
