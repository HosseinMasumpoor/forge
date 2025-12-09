<?php
namespace Modules\User\Repositories\Eloquent;

use Modules\Core\app\Repositories\BaseRepository;
use Modules\User\app\Interfaces\Repositories\UserRepositoryInterface;
use Modules\User\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
