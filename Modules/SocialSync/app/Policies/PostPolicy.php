<?php

namespace Modules\SocialSync\app\Policies;

use Carbon\Carbon;
use Modules\SocialSync\app\Enums\PostStatus;
use Modules\SocialSync\app\Models\Post;
use Modules\Subscription\app\Enums\PlanLimitType;
use Modules\Subscription\app\Interfaces\Repositories\UserSubscriptionRepositoryInterface;
use Modules\User\Models\User;

class PostPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct(
        protected UserSubscriptionRepositoryInterface $userSubscriptionRepository
    ) {}

    /**
     * Determine if the user can create posts.
     */
    public function create(User $user): bool
    {
        $subscription = $this->userSubscriptionRepository->getUserSubscriptionWithRelations($user->id);

        if (!$subscription) {
            return false;
        }

        if ($subscription->subscription_expires_at && Carbon::parse($subscription->subscription_expires_at)->isPast()) {
            return false;
        }

        $plan = $subscription->plan;

        if ($plan->limit_type === PlanLimitType::UNLIMITED) {
            return true;
        }

        if ($subscription->usage_period_end && Carbon::parse($subscription->usage_period_end)->isPast()) {
            return true;
        }

        if ($plan->limit && $subscription->current_usage >= $plan->limit) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can view the post.
     */
    public function view(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine if the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id && $post->status !== PostStatus::PUBLISHED;
    }

    /**
     * Determine if the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}

