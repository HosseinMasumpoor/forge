<?php

namespace Modules\Subscription\app\Http\Controllers\V1;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Subscription\app\Services\OfferService;
use Modules\Subscription\app\Services\PlanService;

class PlanController extends CoreController
{
    public function __construct(
        private readonly PlanService $service,
        private readonly OfferService $offerService,
    ) {}

    public function list(Request $request)
    {
        $data = $this->service->list()->get();
        return successResponse($data);
    }

    public function show(string $id)
    {
        $data = $this->service->getWithOffers($id);
        if (!$data) {
            return failedResponse(__('Plan not found'), 404);
        }
        return successResponse($data);
    }

    public function offers(Request $request, string $planId)
    {
        $itemsPerPage = $request["itemsPerPage"] ?? config('app.default_paginate_number', 10);
        $data = $this->offerService->getByPlanId($planId)->paginate($itemsPerPage);
        return successResponse($data);
    }
}
