<?php

namespace Modules\Subscription\app\Enums;

use BenSampo\Enum\Enum;

final class PlanLimitType extends Enum
{
    const DAILY = "daily";
    const WEEKLY = "weekly";
    const MONTHLY = "monthly";
    const UNLIMITED = "unlimited";
}


