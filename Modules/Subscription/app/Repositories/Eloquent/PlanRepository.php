<?php
namespace Modules\Subscription\app\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\app\Repositories\BaseRepository;
use Modules\Subscription\app\Enums\SubscriptionOfferStatus;
use Modules\Subscription\app\Interfaces\Repositories\PlanRepositoryInterface;
use Modules\Subscription\app\Models\Plan;
use Modules\User\app\Interfaces\Repositories\UserRepositoryInterface;
use Modules\User\Models\User;

class PlanRepository extends BaseRepository implements PlanRepositoryInterface
{
    public function __construct(Plan $model)
    {
        parent::__construct($model);
    }

    public function allItemsWithOffers(): Builder
    {
        return parent::index()->with(['offers' => function ($query) {
            $query->where('status', SubscriptionOfferStatus::ACTIVE);
        }]);
    }

    public function getWithOffers($id)
    {
        $item = $this->findByField('id', $id);
        if(!$item) {
            return false;
        }

        return $item->load(['offers' => function ($query) {
            $query->where('status', SubscriptionOfferStatus::ACTIVE);
        }]);
    }
}
