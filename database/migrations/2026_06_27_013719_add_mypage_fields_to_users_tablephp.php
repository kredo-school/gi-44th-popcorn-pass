<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('username');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('avatar')->nullable()->after('phone');
            $table->unsignedInteger('points')->default(0)->after('avatar');
            $table->string('gender')->nullable()->after('points');
            $table->string('occupation')->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'avatar',
                'points',
                'gender',
                'occupation',
            ]);
        });
    }
};