<?php

namespace Modules\Subscription\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\app\Events\OrderPlan;
use Modules\SocialSync\app\Events\PostCreated;
use Modules\Subscription\app\Listeners\ActivateSubscription;
use Modules\Subscription\app\Listeners\IncreaseUsage;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        OrderPlan::class => [
            ActivateSubscription::class,
        ],

        PostCreated::class => [
            IncreaseUsage::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = false;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
