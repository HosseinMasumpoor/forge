<?php

namespace Modules\Order\app\Services\Gateway;


use Modules\Order\app\Interfaces\GatewayInterface;

class GatewayResolver
{
    public static function resolve($gateway): ?GatewayInterface
    {
        return match ($gateway) {
            default => app(Test::class),
        };
    }
}
