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
            $table->char('post_id', 36)->nullable();
            $table->char('user_id', 36);
            $table->text('body');
            $table->boolean('spoiler_flag')->default(false);
            $table->unsignedBigInteger('parent_reply_id')->nullable();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_reply_id')->references('id')->on('post_replies')->onDelete('cascade');
            $table->index('post_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_replies');
    }
};