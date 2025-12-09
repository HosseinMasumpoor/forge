<?php

namespace Modules\SocialSync\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\SocialSync\app\Models\Post;

class PostCreated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Post $post
    ) {}
}
