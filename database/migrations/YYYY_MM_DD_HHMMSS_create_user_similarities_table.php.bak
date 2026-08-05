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
            $table->char('user_id_1', 36);
            $table->char('user_id_2', 36);
            $table->float('similarity_score')->default(0); // 0-1 scale
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id_2')->references('id')->on('users')->onDelete('cascade');

            // Indexes for performance
            $table->index(['user_id_1', 'similarity_score']);
            $table->index(['user_id_2', 'similarity_score']);

            // Unique constraint (avoid duplicates, always store smaller ID first)
            $table->unique([
                DB::raw('LEAST(user_id_1, user_id_2)'),
                DB::raw('GREATEST(user_id_1, user_id_2)')
            ]);
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