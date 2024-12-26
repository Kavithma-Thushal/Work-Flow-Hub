<?php

namespace App\Repositories\EmployeeLeave;

use App\Repositories\CrudRepositoryInterface;

interface EmployeeLeaveRepositoryInterface extends CrudRepositoryInterface
{
    public function getByEmployeeId(int $id);
}
