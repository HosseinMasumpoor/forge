<?php

namespace Modules\User\Services\SMS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\User\Interfaces\SendOTPInterface;

class KavenegarSMSService implements SendOTPInterface,SendSMSInterface
{
    private string $key;
    public function __construct()
    {
        $this->key = config('user.kavenegar.apikey');
    }

    public function sendOTP(string $mobile, string $code): bool
    {
        return true;
    }

    public function sendSMS(string $mobile, string $message, string $op = null): bool
    {
        return true;
    }
}
