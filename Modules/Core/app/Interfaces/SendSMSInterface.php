<?php

namespace Modules\Core\Interfaces;

interface SendSMSInterface
{
    public function sendSMS(string $mobile, string $message, string $op = null): bool;
}
