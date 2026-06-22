<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // General
            $table->string('site_name')->default('Popcorn Pass');
            $table->string('support_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('timezone')->default('Asia/Tokyo');

            // Email
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('notification_email')->nullable();

            // Payment
            $table->string('payment_gateway')->default('stripe');
            $table->string('currency')->default('USD');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->string('stripe_publishable_key')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};