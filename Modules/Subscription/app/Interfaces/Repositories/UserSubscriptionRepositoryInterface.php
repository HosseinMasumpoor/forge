<?php
namespace Modules\Subscription\app\Interfaces\Repositories;

use Modules\Subscription\app\Models\UserSubscription;

interface UserSubscriptionRepositoryInterface
{
    public function getByFields($fields);

    public function findByField($field, $value);

    public function newItem($data);

    public function updateItem($id, $data);

    public function remove($id);

    public function index();

    public function getUserSubscription(string $userId);

    public function getUserSubscriptionWithRelations(string $userId);

    public function getRemainingSubscription(string $userId): ?array;
}
