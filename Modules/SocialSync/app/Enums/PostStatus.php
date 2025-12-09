<?php

namespace Modules\SocialSync\app\Enums;

use BenSampo\Enum\Enum;

final class PostStatus extends Enum
{
    const DRAFT = "draft";
    const SCHEDULED = "scheduled";
    const PUBLISHED = "published";
    const FAILED = "failed";
    const PENDING = "pending";
}

