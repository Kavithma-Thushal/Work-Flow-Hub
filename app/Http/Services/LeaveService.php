<?php

namespace App\Http\Services;

use App\Repositories\EmployeeLeave\EmployeeLeaveRepositoryInterface;
use Exception;
use App\Enums\HttpStatus;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LeaveService
{
    protected EmployeeLeaveRepositoryInterface $employeeLeaveRepositoryInterface;

    public function __construct(EmployeeLeaveRepositoryInterface $leaveRepositoryInterface)
    {
        $this->employeeLeaveRepositoryInterface = $leaveRepositoryInterface;
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $leave = $this->employeeLeaveRepositoryInterface->store($data);
            DB::commit();
            return $leave;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Leave store failed: ' . $e->getMessage());
        }
    }

    public function getByEmployeeId(int $id)
    {
        return $this->employeeLeaveRepositoryInterface->getByEmployeeId($id);
    }
}
