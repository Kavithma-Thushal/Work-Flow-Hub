<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\CrudRepository;

class UserRepository extends CrudRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getByEmail(string $email): ?object
    {
        return $this->model->where('email', $email)->first();
    }
}
