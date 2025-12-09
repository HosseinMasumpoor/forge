<?php

namespace Modules\SocialSync\app\Policies;

use Modules\SocialSync\app\Models\SocialAccount;
use Modules\User\Models\User;

class SocialAccountPolicy
{
    /**
     * Determine whether the user can update the social account.
     */
    public function update(User $user, SocialAccount $socialAccount): bool
    {
        return $user->id === $socialAccount->user_id;
    }

    /**
     * Determine whether the user can delete the social account.
     */
    public function delete(User $user, SocialAccount $socialAccount): bool
    {
        return $user->id === $socialAccount->user_id;
    }
}


