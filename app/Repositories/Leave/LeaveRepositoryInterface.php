<?php

namespace App\Repositories\Leave;

use App\Models\Leave;
use App\Repositories\CrudRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface LeaveRepositoryInterface extends CrudRepositoryInterface
{
    public function getByEmployeeId(int $id): Collection;

    public function getByEmployeeIdAndPolicyId(int $employee_id, int $leave_policy_id): ?Leave;
}
