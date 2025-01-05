<?php

namespace App\Http\Services;

use App\Repositories\Company\CompanyRepositoryInterface;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Repositories\EmployeeLeave\EmployeeLeaveRepositoryInterface;
use App\Repositories\PolicyHasLeave\PolicyHasLeaveRepositoryInterface;
use Exception;
use App\Enums\HttpStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeLeaveService
{
    protected CompanyRepositoryInterface $companyRepositoryInterface;
    protected EmployeeRepositoryInterface $employeeRepositoryInterface;
    protected EmployeeLeaveRepositoryInterface $employeeLeaveRepositoryInterface;
    protected PolicyHasLeaveRepositoryInterface $policyHasLeaveRepositoryInterface;

    public function __construct(CompanyRepositoryInterface $companyRepositoryInterface,EmployeeRepositoryInterface $employeeRepositoryInterface, EmployeeLeaveRepositoryInterface $leaveRepositoryInterface, PolicyHasLeaveRepositoryInterface $policyHasLeaveRepositoryInterface)
    {
        $this->companyRepositoryInterface = $companyRepositoryInterface;
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
        $this->employeeLeaveRepositoryInterface = $leaveRepositoryInterface;
        $this->policyHasLeaveRepositoryInterface = $policyHasLeaveRepositoryInterface;
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            // Retrieve the authenticated user's ID
            $userId = Auth::id();

            // Retrieve the employee record using the user ID
            $employee = $this->employeeRepositoryInterface->getByUserId($userId);

            if (!$employee) {
                throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
            }

            // Add the employee_id to the data
            $data['employee_id'] = $employee->id;

            // Store the leave record
            $leave = $this->employeeLeaveRepositoryInterface->store($data);

            DB::commit();
            return $leave;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Leave store failed: ' . $e->getMessage());
        }
    }

    public function getById(int $id)
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::id();

        // Retrieve the company record associated with the authenticated user
        $company = $this->companyRepositoryInterface->getByUserId($userId);

        // Retrieve employee record
        $employee = $this->employeeRepositoryInterface->getById($id);

        if (!$employee) {
            throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
        }

        // Check if the employee belongs to the authenticated company
        if ($employee->company_id !== $company->id) {
            throw new HttpException(HttpStatus::FORBIDDEN, 'Employee does not belong to your company.');
        }

        // Retrieve all leave records for the employee
        $employeeLeaves = $this->employeeLeaveRepositoryInterface->getByEmployeeId($id);

        if ($employeeLeaves->isEmpty()) {
            // If no leaves are recorded, get policy amounts directly
            $casualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyAndType($employee->leave_policy_id, 1);
            $annualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyAndType($employee->leave_policy_id, 2);

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
        $casualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyAndType($employee->leave_policy_id, 1);
        $annualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyAndType($employee->leave_policy_id, 2);

        // Calculate the remaining leaves
        $remainingCasualLeaves = $casualLeavePolicy - $totalCasualLeavesTaken;
        $remainingAnnualLeaves = $annualLeavePolicy - $totalAnnualLeavesTaken;

        return [
            'employee_id' => $id,
            'remaining_casual_leaves' => max($remainingCasualLeaves, 0),
            'remaining_annual_leaves' => max($remainingAnnualLeaves, 0),
        ];
    }

    public function getAll()
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::id();

        // Retrieve employee record using the user ID
        $employee = $this->employeeRepositoryInterface->getByUserId($userId);

        if (!$employee) {
            throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
        }

        // Retrieve all leave records for the employee
        $employeeLeaves = $this->employeeLeaveRepositoryInterface->getByEmployeeId($employee->id);

        if ($employeeLeaves->isEmpty()) {
            // If no leaves are recorded, get policy amounts directly
            $casualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyAndType($employee->leave_policy_id, 1);
            $annualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyAndType($employee->leave_policy_id, 2);

            return [
                'employee_id' => $employee->id,
                'remaining_casual_leaves' => $casualLeavePolicy,
                'remaining_annual_leaves' => $annualLeavePolicy,
            ];
        }

        // Calculate leave usage based on recorded leaves
        $totalCasualLeavesTaken = $employeeLeaves->where('leave_type_id', 1)->count();
        $totalAnnualLeavesTaken = $employeeLeaves->where('leave_type_id', 2)->count();

        // Retrieve policy amounts
        $casualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyAndType($employee->leave_policy_id, 1);
        $annualLeavePolicy = $this->policyHasLeaveRepositoryInterface->getAmountByPolicyAndType($employee->leave_policy_id, 2);

        // Calculate the remaining leaves
        $remainingCasualLeaves = $casualLeavePolicy - $totalCasualLeavesTaken;
        $remainingAnnualLeaves = $annualLeavePolicy - $totalAnnualLeavesTaken;

        return [
            'employee_id' => $employee->id,
            'remaining_casual_leaves' => max($remainingCasualLeaves, 0),
            'remaining_annual_leaves' => max($remainingAnnualLeaves, 0),
        ];
    }
}
