<?php

namespace Modules\Order\app\Enums;

use BenSampo\Enum\Enum;

final class TransactionStatus extends Enum
{
    const PENDING = "pending";
    const SUCCESS = "success";
    const FAILED = "failed";
    const CANCELLED = "cancelled";
}
