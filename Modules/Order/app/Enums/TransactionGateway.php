<?php

namespace Modules\Order\app\Enums;

use BenSampo\Enum\Enum;

final class TransactionGateway extends Enum
{
    const ZARINPAL = "zarinpal";
    const SAMAN = "saman";
    const MELLAT = "mellat";
    const TEST = "test";
}
