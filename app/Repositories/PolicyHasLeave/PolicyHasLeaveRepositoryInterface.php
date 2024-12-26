<?php

namespace App\Repositories\PolicyHasLeave;

use App\Repositories\CrudRepositoryInterface;

interface PolicyHasLeaveRepositoryInterface extends CrudRepositoryInterface
{
    public function getAmountByPolicyIdAndType(int $policyId, int $leaveTypeId);
}
