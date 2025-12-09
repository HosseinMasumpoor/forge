<?php
namespace Modules\Subscription\app\Interfaces\Repositories;

interface PlanRepositoryInterface
{
    public function getByFields($fields);

    public function findByField($field, $value);

    public function newItem($data);

    public function updateItem($id, $data);

    public function remove($id);

    public function index();

    public function allItemsWithOffers();

    public function getWithOffers($id);
}
