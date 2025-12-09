<?php

namespace Modules\SocialSync\app\Interfaces\Repositories;


interface SocialAccountRepositoryInterface
{
    public function getByFields($fields);

    public function findByField($field, $value);

    public function newItem($data);

    public function updateItem($id, $data);

    public function remove($id);

    public function index();
    public function getByUserId(string $userId);

    public function getByProvider(string $userId, string $provider);

    public function updateOrCreate(array $conditions, array $data);
}

