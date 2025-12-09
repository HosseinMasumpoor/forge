<?php

namespace Modules\SocialSync\app\Services\TextGeneratorProviders;

use Modules\SocialSync\app\Interfaces\Repositories\SocialAccountRepositoryInterface;

class SocialAccountService
{
    public function __construct(
        protected SocialAccountRepositoryInterface $repository,
    ) {}

    public function list()
    {
        return $this->repository->index();
    }

    public function getById(string $id)
    {
        return $this->repository->findByField('id', $id);
    }

    public function getByUserId(string $userId)
    {
        return $this->repository->getByUserId($userId);
    }

    public function create(array $data)
    {
        return $this->repository->newItem($data);
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->repository->updateOrCreate($conditions, $data);
    }

    public function update(string $id, array $data)
    {
        return $this->repository->updateItem($id, $data);
    }

    public function delete(string $id)
    {
        return $this->repository->remove($id);
    }

}
