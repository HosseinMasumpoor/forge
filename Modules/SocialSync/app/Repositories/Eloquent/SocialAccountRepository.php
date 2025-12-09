<?php

namespace Modules\SocialSync\app\Repositories\Eloquent;

use Modules\Core\app\Repositories\BaseRepository;
use Modules\SocialSync\app\Interfaces\Repositories\SocialAccountRepositoryInterface;
use Modules\SocialSync\app\Models\SocialAccount;

class SocialAccountRepository extends BaseRepository implements SocialAccountRepositoryInterface
{
    public function __construct(SocialAccount $model)
    {
        parent::__construct($model);
    }

    public function getByUserId(string $userId)
    {
        return $this->query()->where('user_id', $userId);
    }

    public function getByProvider(string $userId, string $provider)
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('provider', $provider)
            ->first();
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->model::updateOrCreate($conditions, $data);
    }
}

