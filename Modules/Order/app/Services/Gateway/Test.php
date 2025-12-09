<?php

namespace Modules\Order\app\Services\Gateway;


use Modules\Order\app\Enums\TransactionStatus;
use Modules\Order\app\Interfaces\GatewayInterface;
use Modules\Order\app\Repositories\Eloquent\TransactionRepository;

class Test implements GatewayInterface
{
    public function pay($transaction): array
    {
        $token = \Str::random(16);
        $refNum = \Str::random(16);

        return [
            'success' => true,
            'redirect_url' => route('api.payment.verify', ['token' => $token, 'ref_num' => $refNum]),
            'payment_id' => $token
        ];
    }

    public function verify($transaction, $refNum): bool
    {

        $rrn = \Str::random(20);
        $refNum = \Str::random(16);

        $transactionRepository = app(TransactionRepository::class);


        $anotherTransaction = $transactionRepository->findByField('ref_id', $refNum);

        if($anotherTransaction){
            return false;
        }


        $transactionRepository->updateItem($transaction->id, [
            'ref_id' => $refNum,
            'rrn' => $rrn,
            'status' => TransactionStatus::SUCCESS
        ]);

        return true;
    }
}
