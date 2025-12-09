<?php

namespace Modules\SocialSync\app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\SocialSync\app\Interfaces\Repositories\PostRepositoryInterface;

class ScheduleChangeablePost implements ValidationRule
{
    public function __construct(private string $userId)
    {
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $repository = app()->make(PostRepositoryInterface::class);
        $item = $repository->findByField('id', $value);

        if(!$item || $this->userId != $item->user_id){
            $fail(__('socialsync::validation.post.post_owner_error'));
        }
    }
}
