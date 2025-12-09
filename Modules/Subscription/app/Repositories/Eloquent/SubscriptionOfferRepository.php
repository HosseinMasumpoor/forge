<?php

namespace Modules\Subscription\app\Repositories\Eloquent;

use Modules\Core\app\Repositories\BaseRepository;
use Modules\Subscription\app\Enums\SubscriptionOfferStatus;
use Modules\Subscription\app\Interfaces\Repositories\SubscriptionOfferRepositoryInterface;
use Modules\Subscription\app\Models\SubscriptionOffer;

class SubscriptionOfferRepository extends BaseRepository implements SubscriptionOfferRepositoryInterface
{
    public function __construct(SubscriptionOffer $model)
    {
        parent::__construct($model);
    }

    public function getByPlanId($planId)
    {
        return $this->index()->where(['plan_id', $planId, 'status' => SubscriptionOfferStatus::ACTIVE]);
    }
}
