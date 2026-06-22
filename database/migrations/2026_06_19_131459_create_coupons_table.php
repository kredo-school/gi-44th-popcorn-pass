<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->integer('discount_percent')->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->string('coupon_type', 50)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('current_uses')->default(0);
            $table->string('coupon_status', 30)->default('active');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->uuid('issued_by_id')->nullable();
            $table->timestamps();

            $table->foreign('issued_by_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};