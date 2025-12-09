<?php

namespace Modules\SocialSync\app\Interfaces;

interface ImageGeneratorInterface
{
    public function generate(string $subject, array $settings = []): ?string;
}
