<?php

namespace Modules\Order\app\Enums;

use BenSampo\Enum\Enum;

final class OrderStatus extends Enum
{
    const PENDING = "pending";
    const PROCESSING = "processing";
    const COMPLETED = "completed";
    const CANCELLED = "cancelled";
    const FAILED = "failed";
}
