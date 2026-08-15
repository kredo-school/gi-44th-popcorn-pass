<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_seats', function (Blueprint $table) {
            $table->timestamp('cancelled_at')
                ->nullable()
                ->after('price_at_reservation');

            $table->index('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('reservation_seats', function (Blueprint $table) {
            $table->dropIndex(['cancelled_at']);
            $table->dropColumn('cancelled_at');
        });
    }
};
