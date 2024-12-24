<?php

namespace App\Repositories\Leave;

use App\Repositories\CrudRepositoryInterface;

interface LeaveRepositoryInterface extends CrudRepositoryInterface
{
    public function getByEmployeeId(int $id);
}
