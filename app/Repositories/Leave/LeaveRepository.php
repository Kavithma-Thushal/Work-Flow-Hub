<?php

namespace App\Repositories\Leave;

use App\Models\Leave;
use App\Repositories\CrudRepository;

class LeaveRepository extends CrudRepository implements LeaveRepositoryInterface
{
    public function __construct(Leave $model)
    {
        parent::__construct($model);
    }
}
