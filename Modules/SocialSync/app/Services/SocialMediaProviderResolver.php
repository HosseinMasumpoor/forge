<?php

namespace Modules\SocialSync\app\Services;

use Modules\SocialSync\app\Interfaces\SocialMediaProviderInterface;
use Modules\SocialSync\app\Services\SocialMediaProviders\InstagramProvider;
use Modules\SocialSync\app\Services\SocialMediaProviders\TwitterProvider;

class SocialMediaProviderResolver
{
    public static function resolve(string $platform): ?SocialMediaProviderInterface
    {
        return match ($platform) {
            'instagram' => new InstagramProvider(),
            'twitter' => new TwitterProvider(),
            default => null
        };
    }
}
