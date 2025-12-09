<?php

namespace Modules\Order\app\Repositories\Eloquent;

use Modules\Core\app\Repositories\BaseRepository;
use Modules\Order\app\Interfaces\Repositories\OrderItemRepositoryInterface;
use Modules\Order\app\Models\OrderItem;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }
}

