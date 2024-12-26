<?php

namespace App\Repositories\PolicyHasLeave;

use App\Models\PolicyHasLeave;
use App\Repositories\CrudRepository;

class PolicyHasLeaveRepository extends CrudRepository implements PolicyHasLeaveRepositoryInterface
{
    public function __construct(PolicyHasLeave $model)
    {
        parent::__construct($model);
    }

    public function getAmountByPolicyIdAndType(int $policyId, int $leaveTypeId)
    {
        return PolicyHasLeave::where('leave_policy_id', $policyId)->where('leave_type_id', $leaveTypeId)->first();
    }
}
