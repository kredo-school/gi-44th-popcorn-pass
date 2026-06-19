<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reservation_id');
            $table->uuid('coupon_id')->nullable();
            $table->uuid('promotion_id')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('amount', 10, 2);
            $table->string('payment_status', 30)->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id')->unique()->nullable();
            $table->string('stripe_payment_intent_id')->unique()->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('reservation_id')->references('id')->on('reservations');
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->foreign('promotion_id')->references('id')->on('promotions');
            $table->index('reservation_id');
            $table->index('payment_status');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};