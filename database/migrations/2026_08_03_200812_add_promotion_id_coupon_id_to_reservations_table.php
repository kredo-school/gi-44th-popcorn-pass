<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reservations', 'promotion_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->char('promotion_id', 36)
                    ->nullable()
                    ->after('guest_phone');
            });
        }

        if (!Schema::hasColumn('reservations', 'coupon_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->char('coupon_id', 36)
                    ->nullable()
                    ->after('promotion_id');
            });
        }
    }


    public function down(): void
    {
        if (Schema::hasColumn('reservations', 'coupon_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('coupon_id');
            });
        }

        if (Schema::hasColumn('reservations', 'promotion_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('promotion_id');
            });
        }
    }
};
