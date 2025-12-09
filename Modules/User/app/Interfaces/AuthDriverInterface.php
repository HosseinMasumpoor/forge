<?php

namespace Modules\User\Interfaces;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthDriverInterface
{
    public function generateToken(Authenticatable $user, string $guard = 'user'): string;
    public function getTTLBySeconds(): int;

    public function refreshToken(string $token, string $guard = 'user'): string;

    public function invalidateToken();
}
