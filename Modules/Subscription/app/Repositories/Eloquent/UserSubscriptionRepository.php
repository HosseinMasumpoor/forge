<?php
namespace Modules\Subscription\app\Repositories\Eloquent;

use Modules\Core\app\Repositories\BaseRepository;
use Modules\Subscription\app\Interfaces\Repositories\UserSubscriptionRepositoryInterface;
use Modules\Subscription\app\Models\UserSubscription;
use Modules\User\app\Interfaces\Repositories\UserRepositoryInterface;
use Modules\User\Models\User;

class UserSubscriptionRepository extends BaseRepository implements UserSubscriptionRepositoryInterface
{
    public function __construct(UserSubscription $model)
    {
        parent::__construct($model);
    }

    public function getUserSubscription($userId)
    {
        return $this->findByField('user_id', $userId)->first();
    }

    public function getUserSubscriptionWithRelations($userId)
    {
        $item = $this->findByField('user_id', $userId);
        if(!$item){
            return null;
        }

        return $item->load(['plan', 'offer']);
    }

    public function getRemainingSubscription($userId): ?array
    {
        $item = $this->findByField('user_id', $userId);
        if(!$item){
            return null;
        }

        $limit = $item->plan->limit;
        $usage = $item->current_usage;

        return [
            'limit' => $limit,
            'remaining' => $limit - $usage,
            'usage' => $usage,
        ];
    }
}
