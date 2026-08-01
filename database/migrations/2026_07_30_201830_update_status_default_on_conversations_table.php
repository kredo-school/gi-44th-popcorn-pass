<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {

            $table->string('status')
                ->default('ai')
                ->change();
        });
    }


    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {

            $table->string('status')
                ->change();
        });
    }
};