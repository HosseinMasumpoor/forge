<?php
namespace Modules\Core\app\Interfaces;


interface BaseRepositoryInterface
{
    public function getByFields($fields);

    public function findByField($field, $value);

    public function newItem($data);

    public function updateItem($id, $data);

    public function remove($id);

    public function index();
}
