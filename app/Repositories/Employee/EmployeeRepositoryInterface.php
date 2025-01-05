<?php

namespace App\Repositories\Employee;

use App\Repositories\CrudRepositoryInterface;

interface EmployeeRepositoryInterface extends CrudRepositoryInterface
{
    public function getByUserId(int $userId);

    public function getByCompanyId(int $companyId);
}
