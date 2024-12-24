<?php

namespace App\Http\Services;

use App\Repositories\Leave\LeaveRepositoryInterface;
use App\Repositories\LeavePolicy\LeavePolicyRepositoryInterface;
use Exception;
use App\Enums\HttpStatus;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LeaveService
{
    protected EmployeeRepositoryInterface $employeeRepositoryInterface;
    protected LeaveRepositoryInterface $leaveRepositoryInterface;
    protected LeavePolicyRepositoryInterface $leavePolicyRepositoryInterface;

    public function __construct(EmployeeRepositoryInterface $employeeRepositoryInterface, LeaveRepositoryInterface $leaveRepositoryInterface, LeavePolicyRepositoryInterface $leavePolicyRepositoryInterface)
    {
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
        $this->leaveRepositoryInterface = $leaveRepositoryInterface;
        $this->leavePolicyRepositoryInterface = $leavePolicyRepositoryInterface;
    }

    public function add(array $data)
    {
        DB::beginTransaction();
        try {
            $leave = $this->leaveRepositoryInterface->store([
                'employee_id' => $data['employee_id'],
                'taken_casual_leaves' => $data['casual_leaves'],
                'taken_annual_leaves' => $data['annual_leaves'],
            ]);

            DB::commit();
            return $leave;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Leave save failed: ' . $e->getMessage());
        }
    }

    public function getByEmployeeId(int $id)
    {
        $leaves = $this->leaveRepositoryInterface->getByEmployeeId($id);

        if (!$leaves) {
            throw new HttpException(HttpStatus::NOT_FOUND, 'No leaves found for this employee');
        }

        return $leaves;
    }
}
