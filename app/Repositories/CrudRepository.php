<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class CrudRepository implements CrudRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function save(array $data): ?object
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?object
    {
        $record = $this->model->find($id);
        if ($record) {
            $record->update($data);
        }
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        return $record ? $record->delete() : false;
    }

    public function getById(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function getAll(): array
    {
        return $this->model->all()->toArray();
    }
}
