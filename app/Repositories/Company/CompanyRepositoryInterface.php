<?php

namespace App\Repositories\Company;

use App\Repositories\CrudRepositoryInterface;

interface CompanyRepositoryInterface extends CrudRepositoryInterface
{
    public function getByUserId($userId);
}
