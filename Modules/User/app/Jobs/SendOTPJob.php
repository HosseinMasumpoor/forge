<?php

namespace Modules\User\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Interfaces\SendOTPInterface;
use Modules\User\Interfaces\SendOTPInterface;
use Throwable;

class SendOTPJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public int $retryAfter = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $mobile, public string $code)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SendOTPInterface $sendOTPService): void
    {
        $result = $sendOTPService->sendOTP($this->mobile, $this->code);
        if(!$result){
            throw new \Exception("Failed to send OTP to {$this->mobile}.");
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Send OTP SMS failed after 5 attempts.', [
            'mobile' => $this->mobile,
            'error' => $exception->getMessage(),
        ]);
    }
}
