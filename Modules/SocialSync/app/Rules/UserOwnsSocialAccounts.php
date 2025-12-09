<?php

namespace Modules\SocialSync\app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\SocialSync\app\Models\SocialAccount;

class UserOwnsSocialAccounts implements ValidationRule
{
    public function __construct(protected string $userId)
    {
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validAccountIds = SocialAccount::query()
            ->where('user_id', $this->userId)
            ->pluck('id')
            ->toArray();

        $invalidAccounts = array_diff($value, $validAccountIds);
        if(!empty($invalidAccounts)) {
            $fail(__('socialsync::validation.post.social_account_invalid_owner'));
        }
    }
}
