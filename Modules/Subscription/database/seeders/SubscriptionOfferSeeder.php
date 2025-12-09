<?php

namespace Modules\Subscription\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\app\Enums\SubscriptionOfferStatus;
use Modules\Subscription\app\Models\Plan;
use Modules\Subscription\app\Models\SubscriptionOffer;

class SubscriptionOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = Plan::all();

        foreach ($plans as $plan) {
            // Create offers for different durations (30, 90, 180, 365 days)
            $durations = [30, 90, 180, 365];

            // Base prices per month (approximate)
            $basePrices = [
                1 => 1200000,  // Basic plan
                2 => 1500000,  // Pro plan
                3 => 2000000,   // Unlimited plan
            ];

            $basePrice = $basePrices[$plan->id] ?? 1000000;

            foreach ($durations as $days) {
                $months = $days / 30;
                // Calculate price with discount for longer durations
                $price = $basePrice * $months;
                if ($days >= 180) {
                    $price = $price * 0.9; // 10% discount for 6+ months
                }
                if ($days >= 365) {
                    $price = $price * 0.85; // 15% discount for 12+ months
                }

                SubscriptionOffer::updateOrCreate([
                    'plan_id' => $plan->id,
                    'duration_in_days' => $days,
                ], [
                    'price' => $price,
                    'status' => SubscriptionOfferStatus::ACTIVE,
                ]);
            }
        }
    }
}
