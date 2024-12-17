<?php

namespace App\Repositories;

interface CrudRepositoryInterface
{
    public function save(array $data): ?object;

    public function update(int $id, array $data): ?object;

    public function delete(int $id): bool;

    public function getById(int $id): ?object;

    public function getAll(): array;
}
