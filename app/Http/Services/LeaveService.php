<?php

namespace App\Http\Services;

use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Repositories\EmployeeLeave\EmployeeLeaveRepositoryInterface;
use App\Repositories\PolicyHasLeave\PolicyHasLeaveRepositoryInterface;
use Exception;
use App\Enums\HttpStatus;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LeaveService
{
    protected EmployeeRepositoryInterface $employeeRepositoryInterface;
    protected EmployeeLeaveRepositoryInterface $employeeLeaveRepositoryInterface;
    protected PolicyHasLeaveRepositoryInterface $policyHasLeaveRepositoryInterface;

    public function __construct(EmployeeRepositoryInterface $employeeRepositoryInterface, EmployeeLeaveRepositoryInterface $leaveRepositoryInterface, PolicyHasLeaveRepositoryInterface $policyHasLeaveRepositoryInterface)
    {
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
        $this->employeeLeaveRepositoryInterface = $leaveRepositoryInterface;
        $this->policyHasLeaveRepositoryInterface = $policyHasLeaveRepositoryInterface;
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
        // Retrieve employee record
        $employee = $this->employeeRepositoryInterface->getById($id);

        if (!$employee) {
            throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
        }

        // Retrieve all leave records for the employee
        $employeeLeaves = $this->employeeLeaveRepositoryInterface->getByEmployeeId($id);

        if ($employeeLeaves->isEmpty()) {
            // If no leaves are recorded, get policy amounts directly
            $casualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyIdAndType($employee->leave_policy_id, 1);
            $annualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyIdAndType($employee->leave_policy_id, 2);

            return [
                'employee_id' => $id,
                'remaining_casual_leaves' => $casualLeavePolicy,
                'remaining_annual_leaves' => $annualLeavePolicy,
            ];
        }

        // Calculate leave usage based on recorded leaves
        $totalCasualLeavesTaken = $employeeLeaves->where('leave_type_id', 1)->count();
        $totalAnnualLeavesTaken = $employeeLeaves->where('leave_type_id', 2)->count();

        // Retrieve policy amounts
        $casualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyIdAndType($employee->leave_policy_id, 1);
        $annualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyIdAndType($employee->leave_policy_id, 2);

        // Calculate the remaining leaves
        $remainingCasualLeaves = $casualLeavePolicy - $totalCasualLeavesTaken;
        $remainingAnnualLeaves = $annualLeavePolicy - $totalAnnualLeavesTaken;

        return [
            'employee_id' => $id,
            'remaining_casual_leaves' => max($remainingCasualLeaves, 0),
            'remaining_annual_leaves' => max($remainingAnnualLeaves, 0),
        ];
    }
}
