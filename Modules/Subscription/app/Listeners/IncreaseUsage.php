<?php

namespace Modules\Subscription\app\Listeners;

use Carbon\Carbon;
use Modules\SocialSync\app\Events\PostCreated;
use Modules\Subscription\app\Enums\PlanLimitType;
use Modules\Subscription\app\Interfaces\Repositories\UserSubscriptionRepositoryInterface;
use Modules\Subscription\app\Services\SubscriptionService;

class IncreaseUsage
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected UserSubscriptionRepositoryInterface $userSubscriptionRepository,
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $post = $event->post;

//        if ($post->scheduled_at) {
//            return;
//        }

        $userId = $post->user_id;
        $subscription = $this->userSubscriptionRepository->getUserSubscriptionWithRelations($userId);

        if (!$subscription) {
            return;
        }

        if ($subscription->subscription_expires_at && Carbon::parse($subscription->subscription_expires_at)->isPast()) {
            return;
        }

        $plan = $subscription->plan;

        if ($plan->limit_type === PlanLimitType::UNLIMITED) {
            return;
        }

        $this->initializeUsagePeriodIfNeeded($subscription, $plan);

        $subscription->increment('current_usage');
    }

    protected function initializeUsagePeriodIfNeeded($subscription, $plan): void
    {
        $now = now();

        if (!$subscription->usage_period_start ||
            !$subscription->usage_period_end ||
            Carbon::parse($subscription->usage_period_end)->isPast()) {

            $periodDays = $this->subscriptionService->getPeriodEndDate($plan->limit_type);

            $subscription->update([
                'current_usage' => 0,
                'usage_period_start' => $now->copy()->startOfDay(),
                'usage_period_end' => $now->copy()->addDays($periodDays)->endOfDay(),
            ]);
        }
    }
}

