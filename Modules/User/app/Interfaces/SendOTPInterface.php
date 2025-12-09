<?php

namespace Modules\User\Interfaces;

interface SendOTPInterface
{
    public function sendOTP(string $mobile, string $code): bool;
}
