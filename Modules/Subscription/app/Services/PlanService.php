<?php

namespace Modules\Subscription\app\Services;

use Modules\Subscription\app\Enums\SubscriptionOfferStatus;
use Modules\Subscription\app\Interfaces\Repositories\PlanRepositoryInterface;
use Modules\Subscription\app\Interfaces\Repositories\SubscriptionOfferRepositoryInterface;

class PlanService
{
    public function __construct(
        protected PlanRepositoryInterface $repository,
        protected SubscriptionOfferRepositoryInterface $offerRepository
    ) {}

    public function list()
    {
        return $this->repository->allItemsWithOffers();
    }

    public function getById(string $id)
    {
        return $this->repository->findByField('id', $id);
    }

    public function getWithOffers(string $id)
    {
        return $this->repository->getWithOffers($id);
    }

    public function getOffersByPlan(string $planId)
    {
        return $this->offerRepository->index()
            ->where('plan_id', $planId)
            ->where('status', SubscriptionOfferStatus::ACTIVE);
    }
}
