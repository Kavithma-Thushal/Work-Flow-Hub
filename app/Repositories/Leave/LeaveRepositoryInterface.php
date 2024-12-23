<?php

namespace App\Repositories\Leave;

use App\Repositories\CrudRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface LeaveRepositoryInterface extends CrudRepositoryInterface
{
    public function getByEmployeeId(int $id): Collection;
}
