<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Repositories\CrudRepository;

class CompanyRepository extends CrudRepository implements CompanyRepositoryInterface
{
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }

    public function getByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->first();
    }
}
