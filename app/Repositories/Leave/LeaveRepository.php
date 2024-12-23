<?php

namespace App\Repositories\Leave;

use App\Models\Leave;
use App\Repositories\CrudRepository;
use Illuminate\Database\Eloquent\Collection;

class LeaveRepository extends CrudRepository implements LeaveRepositoryInterface
{
    public function __construct(Leave $model)
    {
        parent::__construct($model);
    }

    public function getByEmployeeId(int $id): Collection
    {
        return $this->model->where('employee_id', $id)->get();
    }

    public function getByEmployeeIdAndPolicyId(int $employee_id, int $leave_policy_id): ?Leave
    {
        return $this->model
            ->where('employee_id', $employee_id)
            ->where('leave_policy_id', $leave_policy_id)
            ->first();
    }
}
