<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {

            $table->char('promotion_id', 36)
                ->nullable()
                ->after('guest_phone');

            $table->char('coupon_id', 36)
                ->nullable()
                ->after('promotion_id');
        });
    }


    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {

            if (Schema::hasColumn('reservations', 'promotion_id')) {
                $table->dropColumn('promotion_id');
            }

            if (Schema::hasColumn('reservations', 'coupon_id')) {
                $table->dropColumn('coupon_id');
            }
        });
    }
};
