<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Modules\Subscription\Database\Factories\PlanFactory;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'limit',
        'limit_type',
        'description',
        'status',
        'order'
    ];

    /**
     * Get the subscription offers for the plan.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(SubscriptionOffer::class);
    }

    // protected static function newFactory(): PlanFactory
    // {
    //     // return PlanFactory::new();
    // }
}
