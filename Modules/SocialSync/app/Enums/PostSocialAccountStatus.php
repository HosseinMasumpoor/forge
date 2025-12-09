<?php

namespace Modules\SocialSync\app\Enums;

use BenSampo\Enum\Enum;

final class PostSocialAccountStatus extends Enum
{
    const PENDING = "pending";
    const PUBLISHED = "published";
    const FAILED = "failed";
    const PROCESSING = "processing";
}

