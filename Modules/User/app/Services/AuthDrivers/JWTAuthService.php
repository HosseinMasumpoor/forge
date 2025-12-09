<?php

namespace Modules\User\Services\AuthDrivers;

use Illuminate\Contracts\Auth\Authenticatable;
use Modules\User\Interfaces\AuthDriverInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthService implements AuthDriverInterface
{
    public function generateToken(Authenticatable $user, string $guard = 'user'): string
    {
        return auth($guard)->login($user);
    }

    public function refreshToken(string $token, string $guard = 'user'): string
    {
        return auth($guard)->refresh();
    }

    public function invalidateToken()
    {
        return JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function getTTLBySeconds(): int
    {
        return JWTAuth::factory()->getTTL() * 60;
    }
}
