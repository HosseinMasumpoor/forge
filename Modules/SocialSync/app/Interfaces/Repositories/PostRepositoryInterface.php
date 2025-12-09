<?php

namespace Modules\SocialSync\app\Interfaces\Repositories;

interface PostRepositoryInterface
{
    public function getByFields($fields);

    public function findByField($field, $value);

    public function newItem($data);

    public function updateItem($id, $data);

    public function remove($id);

    public function index();

    public function getByUserId(string $userId);

    public function getScheduledPosts();

    public function attachSocialAccounts($id, $socialAccountIds);
}

