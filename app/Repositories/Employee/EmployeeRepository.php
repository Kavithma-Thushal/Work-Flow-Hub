<?php

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Repositories\CrudRepository;

class EmployeeRepository extends CrudRepository implements EmployeeRepositoryInterface
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function getByCompanyId(int $companyId)
    {
        return $this->model->where('company_id', $companyId)->get();
    }
}
