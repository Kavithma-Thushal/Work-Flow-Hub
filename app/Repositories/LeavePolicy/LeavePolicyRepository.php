<?php

namespace App\Repositories\LeavePolicy;

use App\Models\LeavePolicy;
use App\Repositories\CrudRepository;

class LeavePolicyRepository extends CrudRepository implements LeavePolicyRepositoryInterface
{
    public function __construct(LeavePolicy $model)
    {
        parent::__construct($model);
    }
}
