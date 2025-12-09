<?php

namespace Modules\Order\app\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Order\app\Enums\OrderStatus;
use Modules\Order\app\Enums\TransactionStatus;
use Modules\Order\app\Enums\TransactionType;
use Modules\Order\app\Events\OrderPlan;
use Modules\Order\app\Interfaces\Repositories\OrderItemRepositoryInterface;
use Modules\Order\app\Interfaces\Repositories\OrderRepositoryInterface;
use Modules\Order\app\Interfaces\Repositories\TransactionRepositoryInterface;
use Modules\Order\app\Services\Gateway\GatewayResolver;
use Modules\Subscription\app\Interfaces\Repositories\SubscriptionOfferRepositoryInterface;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $repository,
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected TransactionRepositoryInterface $transactionRepository,
        protected SubscriptionOfferRepositoryInterface $offerRepository
    ) {}

    public function purchasePlan(string $userId, string $gateway, int $offerId): array
    {
        $offer = $this->offerRepository->findByField('id', $offerId);
        if (!$offer) {
            throw new Exception(__('order::messages.order.item_not_found'));
        }

        // Load plan relationship
        $offer->load('plan');

        try {
            DB::beginTransaction();
            $order = $this->repository->newItem([
                'user_id' => $userId,
                'total_amount' => $offer->price,
                'discount_amount' => 0,
                'status' => OrderStatus::PENDING,
            ]);

            $this->orderItemRepository->newItem([
                'order_id' => $order->id,
                'price' => $offer->price,
                'quantity' => 1,
                'orderable_type' => get_class($offer),
                'orderable_id' => $offer->id,
            ]);

            $transaction = $this->transactionRepository->newItem([
                'order_id' => $order->id,
                'amount' => $offer->price,
                'gateway' => $gateway,
                'status' => TransactionStatus::PENDING,
                'type' => TransactionType::PURCHASE,
            ]);
            DB::commit();
        } catch (Exception $exception) {
            Log::error("Error while purchasing plan", [
                'exception' => $exception,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);
            DB::rollBack();
            throw new Exception(__('order::messages.order.error'));
        }



        $gatewayInstance = GatewayResolver::resolve($gateway);
        $paymentResult = $gatewayInstance->pay($transaction);

        $this->transactionRepository->updateItem($transaction->id, [
            'payment_id' => $paymentResult['payment_id'] ?? null,
            'token' => $paymentResult['payment_id'] ?? null,
        ]);

        return [
            'success' => $paymentResult['success'] ?? true,
            'redirect_url' => $paymentResult['redirect_url'] ?? null,
            'payment_id' => $paymentResult['payment_id'] ?? null,
        ];
    }

    public function verifyPayment(string $token, string $refNum): bool
    {
        $transaction = $this->transactionRepository->findByField('token', $token);
        if (!$transaction) {
            return false;
        }

        $transaction->load('order');

        $gatewayInstance = GatewayResolver::resolve($transaction->gateway);
        $verified = $gatewayInstance->verify($transaction, $refNum);

        if ($verified) {
            $this->transactionRepository->updateItem($transaction->id, [
                'status' => TransactionStatus::SUCCESS,
            ]);

            $order = $transaction->order;
            if ($order) {
                $this->repository->updateItem($order->id, [
                    'status' => OrderStatus::COMPLETED,
                ]);
            }
            event(new OrderPlan($order));
        } else {
            $this->transactionRepository->updateItem($transaction->id, [
                'status' => TransactionStatus::FAILED,
            ]);

            $order = $transaction->order;
            if ($order) {
                $this->repository->updateItem($order->id, [
                    'status' => OrderStatus::FAILED,
                ]);
            }
        }

        return $verified;
    }

    public function list(string $userId)
    {
        return $this->repository->index()->where('user_id', $userId);
    }
}
