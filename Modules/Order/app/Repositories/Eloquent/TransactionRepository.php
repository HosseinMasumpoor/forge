<?php

namespace Modules\Order\app\Repositories\Eloquent;

use Modules\Core\app\Repositories\BaseRepository;
use Modules\Order\app\Interfaces\Repositories\TransactionRepositoryInterface;
use Modules\Order\app\Models\Transaction;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
}

