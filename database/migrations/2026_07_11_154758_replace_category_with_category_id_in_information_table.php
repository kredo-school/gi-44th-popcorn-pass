<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->dropColumn('category');

            $table->uuid('category_id')->nullable()->after('content');

            $table->foreign('category_id')
                ->references('id')
                ->on('information_categories');
        });
    }

    public function down(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');

            $table->string('category')->default('General')->after('content');
        });
    }
};