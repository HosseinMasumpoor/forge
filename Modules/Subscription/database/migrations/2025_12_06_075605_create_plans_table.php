<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Subscription\app\Enums\PlanLimitType;
use Modules\Subscription\app\Enums\PlanStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('limit')->nullable();
            $table->enum('limit_type', PlanLimitType::getValues())->default(PlanLimitType::WEEKLY);
            $table->enum('status', PlanStatus::getValues())->default(PlanStatus::ACTIVE);
            $table->text('description')->nullable();
            $table->smallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
