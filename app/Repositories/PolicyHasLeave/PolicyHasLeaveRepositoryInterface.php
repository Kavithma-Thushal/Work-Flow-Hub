<?php

namespace App\Repositories\PolicyHasLeave;

use App\Repositories\CrudRepositoryInterface;

interface PolicyHasLeaveRepositoryInterface extends CrudRepositoryInterface
{
    public function getAmountByPolicyAndType(int $policyId, int $leaveTypeId);
}
