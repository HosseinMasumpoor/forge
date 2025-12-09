<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Order\app\Enums\TransactionGateway;
use Modules\Order\app\Enums\TransactionStatus;
use Modules\Order\app\Enums\TransactionType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('gateway', TransactionGateway::getValues());
            $table->string('payment_id')->nullable();
            $table->string('ref_id')->nullable();
            $table->string('rrn')->nullable();
            $table->string('token')->nullable();
            $table->enum('status', TransactionStatus::getValues())->default(TransactionStatus::PENDING);
            $table->enum('type', TransactionType::getValues())->default(TransactionType::PURCHASE);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
