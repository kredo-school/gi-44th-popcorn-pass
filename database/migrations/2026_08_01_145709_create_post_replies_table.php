<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_replies', function (Blueprint $table) {

            $table->id();

            // Post ID
            $table->foreignId('post_id')
                ->constrained('posts')
                ->cascadeOnDelete();

            // User ID（UUID）
            $table->char('user_id', 36);

            $table->text('body');

            $table->boolean('spoiler_flag')
                ->default(false);

           
            $table->unsignedBigInteger('parent_reply_id')
                ->nullable();

            $table->timestamps();


            // User
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();


            // Self reference
            $table->foreign('parent_reply_id')
                ->references('id')
                ->on('post_replies')
                ->cascadeOnDelete();


            $table->index('post_id');
            $table->index('created_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('post_replies');
    }
};
