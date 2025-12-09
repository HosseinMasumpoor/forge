<?php

namespace Modules\Subscription\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\app\Enums\PlanLimitType;
use Modules\Subscription\app\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'limit' => 2,
                'limit_type' => PlanLimitType::WEEKLY,
                'order' => 1
            ],
            [
                'name' => 'Pro',
                'limit' => 4,
                'limit_type' => PlanLimitType::WEEKLY,
                'order' => 2
            ],
            [
                'name' => 'Unlimited',
                'limit' => 1,
                'limit_type' => PlanLimitType::DAILY,
                'order' => 3
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate([
                'name' => $plan['name'],
            ], [
                'limit' => $plan['limit'],
                'limit_type' => $plan['limit_type'],
                'order' => $plan['order'],
            ]);
        }
    }
}
