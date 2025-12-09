<?php

namespace Modules\Subscription\app\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Subscription\app\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $service)
    {
    }

    public function getCurrentSubscription(Request $request)
    {
        $userId= auth('user')->id();
        $data = $this->service->getUserSubscription($userId);
        return successResponse($data);
    }

    public function getRemainingSubscription(Request $request)
    {
        $userId= auth('user')->id();
        $data = $this->service->getRemainingSubscription($userId);
        return successResponse($data);
    }
}
