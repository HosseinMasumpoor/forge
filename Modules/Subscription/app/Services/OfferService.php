<?php

namespace Modules\Subscription\app\Services;

use Modules\Subscription\app\Interfaces\Repositories\SubscriptionOfferRepositoryInterface;

class OfferService
{
    public function __construct(
        protected SubscriptionOfferRepositoryInterface $repository
    ) {}

   public function getByPlanId($planId)
   {
       return $this->repository->getByPlanId(["plan_id" => $planId]);
   }
}
