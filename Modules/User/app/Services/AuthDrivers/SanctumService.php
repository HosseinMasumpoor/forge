<?php

namespace Modules\User\Services\AuthDrivers;

use Illuminate\Contracts\Auth\Authenticatable;
use Modules\User\Interfaces\AuthDriverInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

class SanctumService implements AuthDriverInterface
{
    public function generateToken(Authenticatable $user, string $guard = 'user'): string
    {
        return $user->createToken($guard)->plainTextToken;
    }

    public function refreshToken(string $token, string $guard = 'user'): string
    {
        throw new \Exception("Sanctum does not support token refreshing.");
    }

    public function invalidateToken()
    {
        auth()->user()?->tokens()->delete();
        return true;
    }

    public function getTTLBySeconds(): int
    {
        $minutes = config('sanctum.expiration');

        return $minutes ? $minutes * 60 : 0;
    }
}
