<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\User\Exceptions\AuthException;
use Modules\User\Interfaces\SendOTPInterface;
use Modules\User\Jobs\SendOTPJob;

class OTPService
{
    const CACHE_KEY_PREFIX = 'user_mobile_';
    private int $expireTime;

    public function __construct(private readonly SendOTPInterface $sendOTPService)
    {
        $this->expireTime = config('user::otp_expire_time', 180);
    }

    public  function send($mobile): array
    {
        if($this->checkIsSent($mobile)){
            throw AuthException::otpAlreadySent();
        }

        $code = random_int(100000, 999999);
        if(config('app.env') == 'local'){
            $code = "123456";
        }

        Cache::put(self::CACHE_KEY_PREFIX.$mobile, $code, $this->expireTime);

        if(config('async_sms_sending', false)){
            dispatch(new SendOTPJob($mobile, $code));
        }else{
            $this->sendOTPService->sendOTP($mobile, $code);
        }
        Log::info("Sending OTP to mobile number $mobile : $code");

        return [
            'expires_in' => $this->expireTime,
        ];
    }

    public function verify($mobile,$otp): bool
    {
        $cacheKey = self::CACHE_KEY_PREFIX.$mobile;
        if(Cache::get($cacheKey) == $otp){
            Cache::forget($cacheKey);
            return true;
        }
        return false;
    }

    private function checkIsSent($mobile): bool
    {
        return Cache::has(self::CACHE_KEY_PREFIX.$mobile);
    }
}
