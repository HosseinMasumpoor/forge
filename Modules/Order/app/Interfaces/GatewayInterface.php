<?php

namespace Modules\Order\app\Interfaces;


use Modules\Order\app\Models\Transaction;

interface GatewayInterface
{
    public function pay(Transaction $transaction) : array;
    public function verify(Transaction $transaction, string $refNum) : bool;
}
