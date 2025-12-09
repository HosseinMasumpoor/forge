<?php

namespace Modules\Subscription\app\Services;

use Modules\Subscription\app\Enums\PlanLimitType;
use Modules\Subscription\app\Interfaces\Repositories\UserSubscriptionRepositoryInterface;

class SubscriptionService
{
    public function __construct(private UserSubscriptionRepositoryInterface $repository)
    {
    }

    public function getUserSubscription(string $userId)
    {
        return $this->repository->getUserSubscriptionWithRelations($userId);
    }

    public function checkHasPlan(string $userId): bool
    {
        $item = $this->repository->getUserSubscription($userId);
        if(!$item){
            return false;
        }
        return true;
    }

    public function getRemainingSubscription(string $userId): ?array
    {
        return $this->repository->getRemainingSubscription($userId);
    }

    public function getPeriodEndDate(string $limitType): int
    {
        return match ($limitType) {
            PlanLimitType::DAILY => 1,
            PlanLimitType::WEEKLY => 7,
            PlanLimitType::MONTHLY => 30,
            PlanLimitType::UNLIMITED => 1000000,
        };
    }

}
