<?php

namespace App\Repositories\PolicyHasLeave;

use App\Models\PolicyHasType;
use App\Repositories\CrudRepository;

class PolicyHasLeaveRepository extends CrudRepository implements PolicyHasLeaveRepositoryInterface
{
    public function __construct(PolicyHasType $model)
    {
        parent::__construct($model);
    }

    public function getAmountByPolicyAndType(int $policyId, int $leaveTypeId)
    {
        $policyLeave = $this->model->where('leave_policy_id', $policyId)->where('leave_type_id', $leaveTypeId)->first();
        return $policyLeave ? $policyLeave->amount : 0;
    }
}
