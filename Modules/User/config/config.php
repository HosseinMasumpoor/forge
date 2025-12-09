<?php

return [
    'name' => 'User',
    'otp_expire_time' => env('OTP_EXPIRE_TIME', 180),
    'async_otp_sending' => env('ASYNC_OTP_SENDING', false),
];
