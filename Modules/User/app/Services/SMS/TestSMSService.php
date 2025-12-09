<?php

namespace Modules\User\Services\SMS;

use Modules\User\Interfaces\SendOTPInterface;

class TestSMSService implements SendOTPInterface
{
    public function sendOTP(string $mobile, string $code): bool
    {
        return true;
    }
}
