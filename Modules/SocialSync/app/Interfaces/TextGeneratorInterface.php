<?php

namespace Modules\SocialSync\app\Interfaces;

interface TextGeneratorInterface
{
    public function generate(string $subject, array $settings = []): ?string;
}
