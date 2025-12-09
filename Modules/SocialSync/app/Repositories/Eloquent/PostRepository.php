<?php

namespace Modules\SocialSync\app\Repositories\Eloquent;

use Modules\Core\app\Repositories\BaseRepository;
use Modules\SocialSync\app\Enums\PostStatus;
use Modules\SocialSync\app\Interfaces\Repositories\PostRepositoryInterface;
use Modules\SocialSync\app\Models\Post;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function getByUserId(string $userId)
    {
        return $this->query()->where('user_id', $userId);
    }

    public function getScheduledPosts()
    {
        return $this->query()
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }

    public function getDispatchableScheduledPosts()
    {
        return $this->query()->where('status', PostStatus::SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->with('socialAccounts');
    }

    public function attachSocialAccounts($id, $socialAccountIds): void
    {
        $item = $this->findByField('id', $id);
        $item->socialAccounts()->attach($socialAccountIds);
    }
}

