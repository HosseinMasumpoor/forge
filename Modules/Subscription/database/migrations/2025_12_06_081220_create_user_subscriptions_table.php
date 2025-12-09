<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans');
            $table->foreignId(column: 'offer_id')->nullable()->constrained('subscription_offers')->onDelete('cascade');

            $table->unsignedInteger('current_usage')->default(0);
            $table->timestamp('usage_period_start')->nullable();
            $table->timestamp('usage_period_end')->nullable()->index();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
