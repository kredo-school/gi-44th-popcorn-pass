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
        Schema::create('user_similarities', function (Blueprint $table) {
            $table->id();

            // User IDs
            $table->uuid('user_id_1');
            $table->uuid('user_id_2');

            // Similarity score between 0 and 1
            $table->float('similarity_score')->default(0);

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id_1')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('user_id_2')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            // Indexes for performance
            $table->index([
                'user_id_1',
                'similarity_score',
            ]);

            $table->index([
                'user_id_2',
                'similarity_score',
            ]);

            // Prevent duplicate user pairs
            $table->unique(
                ['user_id_1', 'user_id_2'],
                'user_similarities_user_pair_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_similarities');
    }
};
