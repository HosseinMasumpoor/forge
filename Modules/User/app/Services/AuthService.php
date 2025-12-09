<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Hash;
use Modules\User\app\Interfaces\Repositories\UserRepositoryInterface;
use Modules\User\Exceptions\AuthException;
use Modules\User\Interfaces\AuthDriverInterface;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $repository, protected OTPService $otpService, protected AuthDriverInterface $authDriver){}

    public function loginUsingPassword(string $username, string $password): string
    {
        $user = $this->repository->findByField("mobile", $username);
        if(!$user || !Hash::check($password, $user->password)){
            throw AuthException::invalidCredentials();
        }
        return $this->generateToken($user);
    }

    public function loginUsingOTP(string $mobile): array
    {
        return $this->otpService->send(convertToValidMobileNumber($mobile));
    }

    public function verifyOTP(string $mobile, string $code): array
    {
        $registered = false;
        $mobile = convertToValidMobileNumber($mobile);
        $result = $this->otpService->verify($mobile, $code);
        if(!$result){
            throw AuthException::otpInvalid();
        }

        $user = $this->repository->findByField("mobile", $mobile);
        if(!$user){
            $user = $this->repository->newItem([
                "mobile" => $mobile,
            ]);
            $registered = true;
        }

        $token = $this->generateToken($user);

        return [
            "token" => $token,
            "registered" => $registered,
            "token_type" => "bearer",
            "expires_in" => $this->authDriver->getTTLBySeconds()
        ];
    }

    public function refreshToken($token): array
    {
        $token =  $this->authDriver->refreshToken($token);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->authDriver->getTTLBySeconds()
        ];
    }

    public function logout()
    {
        return $this->authDriver->invalidateToken();
    }

    private function generateToken($user): string
    {
        return $this->authDriver->generateToken($user);
    }
}
