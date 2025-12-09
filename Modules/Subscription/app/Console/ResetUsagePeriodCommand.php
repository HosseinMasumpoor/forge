<?php

namespace Modules\Subscription\app\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\app\Enums\PlanLimitType;
use Modules\Subscription\app\Models\UserSubscription;
use Modules\Subscription\app\Services\SubscriptionService;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ResetUsagePeriodCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:reset-usage-period';

    /**
     * The console command description.
     */
    protected $description = 'Resets usage periods for user subscriptions that have expired based on their plan limit type.';

    /**
     * Create a new command instance.
     */
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $subscriptions = UserSubscription::with(['plan', 'offer'])
            ->where(function ($query) use ($now) {
                $query->whereNull('usage_period_end')
                    ->orWhere('usage_period_end', '<=', $now);
            })
            ->get();

        $resetCount = 0;

        foreach ($subscriptions as $subscription) {
            if ($subscription->subscription_expires_at && $subscription->subscription_expires_at->isPast()) {
                continue;
            }

            $plan = $subscription->plan;

            if ($plan->limit_type === PlanLimitType::UNLIMITED) {
                continue;
            }

            $periodDays = $this->subscriptionService->getPeriodEndDate($plan->limit_type);

            $newPeriodStart = $now->copy()->startOfDay();
            $newPeriodEnd = $now->copy()->addDays($periodDays)->endOfDay();

            DB::transaction(function () use ($subscription, $newPeriodStart, $newPeriodEnd) {
                $subscription->update([
                    'current_usage' => 0,
                    'usage_period_start' => $newPeriodStart,
                    'usage_period_end' => $newPeriodEnd,
                ]);
            });

            $resetCount++;
            $this->info("Reset usage period for User #{$subscription->user_id} (Plan: {$plan->name}, Limit Type: {$plan->limit_type})");
        }

        if ($resetCount === 0) {
            $this->info('No usage periods needed to be reset.');
        } else {
            $this->info("Successfully reset {$resetCount} usage period(s).");
        }

        return CommandAlias::SUCCESS;
    }
}
