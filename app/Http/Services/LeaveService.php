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
            // Ensure employee_id is valid
            $employee = $this->employeeRepositoryInterface->getById($data['employee_id']);

            // Ensure leave_policy_id is valid
            $leavePolicy = $this->leavePolicyRepositoryInterface->getById($data['leave_policy_id']);

            if (!$employee || !$leavePolicy) {
                throw new HttpException(HttpStatus::NOT_FOUND, 'Employee or Leave Policy not found.');
            }

            $leave = $this->leaveRepositoryInterface->store([
                'employee_id' => $data['employee_id'],
                'leave_policy_id' => $data['leave_policy_id'],
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
}
