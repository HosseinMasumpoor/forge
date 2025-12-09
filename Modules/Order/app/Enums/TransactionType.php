<?php

namespace Modules\Order\app\Enums;

use BenSampo\Enum\Enum;

final class TransactionType extends Enum
{
    const PURCHASE = "purchase";
    const REFUND = "refund";
    const CHARGE = "charge";
}
