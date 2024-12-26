<?php

namespace App\Http\Services;

use App\Repositories\EmployeeLeave\EmployeeLeaveRepositoryInterface;
use App\Repositories\PolicyHasLeave\PolicyHasLeaveRepositoryInterface;
use Exception;
use App\Enums\HttpStatus;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LeaveService
{
    protected EmployeeLeaveRepositoryInterface $employeeLeaveRepositoryInterface;
    protected PolicyHasLeaveRepositoryInterface $policyHasLeaveRepositoryInterface;

    public function __construct(EmployeeLeaveRepositoryInterface $leaveRepositoryInterface, PolicyHasLeaveRepositoryInterface $policyHasLeaveRepositoryInterface)
    {
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
        // Retrieve all leave records for the employee
        $employeeLeaves = $this->employeeLeaveRepositoryInterface->getByEmployeeId($id);

        // Assume that the policy is already linked with the employee's leave types.
        $casualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyIdAndType($id, 1);
        $annualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyIdAndType($id, 2);

        // Calculate the total taken annual and casual leaves
        $totalCasualLeavesTaken = $employeeLeaves->where('leave_type_id', 1)->count();
        $totalAnnualLeavesTaken = $employeeLeaves->where('leave_type_id', 2)->count();

        // Calculate the remaining annual and casual leaves
        $remainingCasualLeaves = $casualLeavePolicy - $totalCasualLeavesTaken;
        $remainingAnnualLeaves = $annualLeavePolicy - $totalAnnualLeavesTaken;

        return [
            'employee_id' => $id,
            'remaining_casual_leaves' => max($remainingCasualLeaves, 0),
            'remaining_annual_leaves' => max($remainingAnnualLeaves, 0),
        ];
    }
}
