<?php

namespace Modules\User\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Modules\User\Exceptions\AuthException;
use Modules\User\Http\Requests\V1\Auth\LoginRequest;
use Modules\User\Http\Requests\V1\Auth\RefreshTokenRequest;
use Modules\User\Http\Requests\V1\Auth\VerifyOTPRequest;
use Modules\User\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service){}

    public function login(LoginRequest $request){
        $data = $request->validated();
        try {
            if(isset($data["password"])){
                $result = $this->service->loginUsingPassword($data["mobile"], $data["password"]);
            }else{
                $result = $this->service->loginUsingOTP($data["mobile"]);
            }

            return successResponse($result, __("user::messages.otp_sent"));
        }catch (AuthException $e){
            return failedResponse($e->getMessage(), $e->getCode());
        }
    }

    public function verifyOTP(VerifyOTPRequest $request){
        $data = $request->validated();
        try {
            $data = $this->service->verifyOTP($data["mobile"], $data["code"]);
            $token = $data["token"];
            $message = $data["registered"] ? __("user::messages.complete_profile") : __("user::messages.login_successful");
            return authSuccessResponse("Bearer $token", config('sanctum.expiration'), $message);
        }catch (AuthException $e){
            return failedResponse($e->getMessage(), $e->getCode());
        }
    }

    public function refreshToken(RefreshTokenRequest $request)
    {
        $data = $request->validated();

        $result = $this->service->refreshToken($data["refresh_token"]);
        if($result){
            return authSuccessResponse(
                $result["access_token"],
                $result["expires_in"],
                __('user::messages.refresh_successful')
            );
        }
        return failedResponse(__("user::messages.refresh_failed"));
    }

    public function logout()
    {
        $result = $this->service->logout();
        if($result){
            return successResponse($result, __("user::messages.logout_successful"));
        }
        return failedResponse(__("user::messages.logout_failed"));
    }

}
