<?php

namespace App\Repositories\EmployeeLeave;

use App\Models\EmployeeLeave;
use App\Repositories\CrudRepository;

class EmployeeLeaveRepository extends CrudRepository implements EmployeeLeaveRepositoryInterface
{
    public function __construct(EmployeeLeave $model)
    {
        parent::__construct($model);
    }
}
