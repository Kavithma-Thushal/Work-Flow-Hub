<?php

namespace App\Repositories\Leave;

use App\Models\EmployeeLeave;
use App\Repositories\CrudRepository;

class LeaveRepository extends CrudRepository implements LeaveRepositoryInterface
{
    public function __construct(EmployeeLeave $model)
    {
        parent::__construct($model);
    }

    public function getByEmployeeId(int $id)
    {
        return $this->model->where('employee_id', $id)->get();
    }
}
