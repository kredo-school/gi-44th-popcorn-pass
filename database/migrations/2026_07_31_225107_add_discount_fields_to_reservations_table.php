<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'promotion_discount')) {
                $table->decimal('promotion_discount', 10, 2)
                    ->default(0)
                    ->after('subtotal');
            }

            if (!Schema::hasColumn('reservations', 'coupon_discount')) {
                $table->decimal('coupon_discount', 10, 2)
                    ->default(0)
                    ->after('promotion_discount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('reservations', 'promotion_discount')) {
                $columns[] = 'promotion_discount';
            }

            if (Schema::hasColumn('reservations', 'coupon_discount')) {
                $columns[] = 'coupon_discount';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
