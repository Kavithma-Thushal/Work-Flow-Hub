<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface CrudRepositoryInterface
{
    public function store(array $data): ?object;

    public function update(int $id, array $data): ?object;

    public function delete(int $id): bool;

    public function getById(int $id): ?object;

    public function getAll(): Collection;
}
