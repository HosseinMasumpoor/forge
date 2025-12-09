<?php

namespace Modules\Order\app\Repositories\Eloquent;

use Modules\Core\app\Repositories\BaseRepository;
use Modules\Order\app\Interfaces\Repositories\OrderRepositoryInterface;
use Modules\Order\app\Models\Order;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}
