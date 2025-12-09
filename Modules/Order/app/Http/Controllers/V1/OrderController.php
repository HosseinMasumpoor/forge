<?php

namespace Modules\Order\app\Http\Controllers\V1;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\app\Http\Requests\V1\OrderStoreRequest;
use Modules\Order\app\Services\OrderService;

class OrderController extends CoreController
{
    public function __construct(private readonly OrderService $service) {}

    public function list(Request $request)
    {
        $userId = auth('user')->user()->id;
        $itemsPerPage = $request["itemsPerPage"] ?? config('app.default_paginate_number', 10);
        $data = $this->service->list($userId)->paginate($itemsPerPage);
        return successResponse($data);
    }

    public function purchase(OrderStoreRequest $request)
    {
        $data = $request->validated();

        try {
            $userId = auth('user')->user()->id;
            $result = $this->service->purchasePlan($userId, $data["gateway"], $data["offer_id"]);
            return successResponse($result);
        } catch (\Exception $e) {
            return failedResponse($e->getMessage(), 400);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'ref_num' => 'required|string',
        ]);

        try {
            $result = $this->service->verifyPayment($request->token, $request->ref_num);
            if ($result) {
                return redirect(config('order.success_payment_redirect_url'));
            }
            return redirect(config('order.failure_payment_redirect_url'));
        } catch (\Exception $e) {
            return failedResponse($e->getMessage(), 400);
        }
    }
}
