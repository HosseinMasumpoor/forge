<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\Subscription\Database\Factories\UserSubscriptionFactory;

class UserSubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'offer_id',
        'current_usage',
        'usage_period_start',
        'usage_period_end',
        'subscription_expires_at',
    ];

    /**
     * Get the plan that owns the user subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the offer that owns the user subscription.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(SubscriptionOffer::class);
    }

    // protected static function newFactory(): UserSubscriptionFactory
    // {
    //     // return UserSubscriptionFactory::new();
    // }
}
