<?php

namespace Modules\Subscription\app\Listeners;

use Carbon\Carbon;
use Modules\Subscription\app\Models\UserSubscription;
use Modules\Subscription\app\Services\SubscriptionService;

class ActivateSubscription
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $order = $event->order;
        $userId = $order->user_id;
        $offer = $order->items->first()->orderable;

        $subscriptionExpiresAt = Carbon::now()->addDays($offer->duration_in_days);
        $limitDays = $this->subscriptionService->getPeriodEndDate($offer->plan->limit_type);

        UserSubscription::updateOrCreate(
            ['user_id' => $userId],
            [
                'plan_id' => $offer->plan_id,
                'offer_id' => $offer->id,
                'current_usage' => 0,
                'usage_period_start' => Carbon::now(),
                'usage_period_end' => Carbon::now()->addDays($limitDays),
                'subscription_expires_at' => $subscriptionExpiresAt,
            ]
        );
    }
}
