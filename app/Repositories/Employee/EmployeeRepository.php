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
}
