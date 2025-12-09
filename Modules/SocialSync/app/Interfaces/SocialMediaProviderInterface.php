<?php

namespace Modules\SocialSync\app\Interfaces;

interface SocialMediaProviderInterface
{
    public function publish(string $text, string $mediaPath, array $credentials = []): ?string;
    public function connect();
    public function verify(array $data) : ?array;
}
