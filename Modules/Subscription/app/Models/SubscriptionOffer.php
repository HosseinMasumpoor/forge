<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Subscription\app\Enums\SubscriptionOfferStatus;

class SubscriptionOffer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'plan_id',
        'price',
        'duration_in_days',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_in_days' => 'integer',
            'status' => SubscriptionOfferStatus::class,
        ];
    }

    /**
     * Get the plan that owns the subscription offer.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
