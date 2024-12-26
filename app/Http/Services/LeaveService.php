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
            // Is Employee Available
            $employee = $this->employeeRepositoryInterface->getById($data['employee_id']);
            if (!$employee) {
                throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found.');
            }

            // Is EmployeeLeave Policy Available
            $leavePolicy = $employee->leavePolicy;
            if (!$leavePolicy) {
                throw new HttpException(HttpStatus::NOT_FOUND, 'EmployeeLeave policy not found for this employee.');
            }

            // Fetch the total leaves already taken by the employee
            $totalCasualLeavesTaken = $employee->leaves->sum('taken_casual_leaves');
            $totalAnnualLeavesTaken = $employee->leaves->sum('taken_annual_leaves');

            // Check if casual and annual leaves exceed the allowed limits
            $newTotalCasualLeaves = $totalCasualLeavesTaken + $data['casual_leaves'];
            if ($newTotalCasualLeaves > $leavePolicy->casual_leaves) {
                throw new HttpException(HttpStatus::BAD_REQUEST, 'Casual leaves exceeded the allowed limit.');
            }

            $newTotalAnnualLeaves = $totalAnnualLeavesTaken + $data['annual_leaves'];
            if ($newTotalAnnualLeaves > $leavePolicy->annual_leaves) {
                throw new HttpException(HttpStatus::BAD_REQUEST, 'Annual leaves exceeded the allowed limit.');
            }

            // Store the leave record
            $leave = $this->leaveRepositoryInterface->store([
                'employee_id' => $data['employee_id'],
                'taken_casual_leaves' => $data['casual_leaves'],
                'taken_annual_leaves' => $data['annual_leaves'],
            ]);

            DB::commit();
            return $leave;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'EmployeeLeave save failed: ' . $e->getMessage());
        }
    }

    public function getByEmployeeId(int $id)
    {
        // Fetch the employee's leave records
        $leaves = $this->leaveRepositoryInterface->getByEmployeeId($id);
        if ($leaves->isEmpty()) {
            throw new HttpException(HttpStatus::NOT_FOUND, 'No leaves found for this employee');
        }

        // Fetch the employee's leave policy
        $employee = $this->employeeRepositoryInterface->getById($id);
        if (!$employee || !$employee->leavePolicy) {
            throw new HttpException(HttpStatus::NOT_FOUND, 'EmployeeLeave policy not found for this employee');
        }

        // Calculate the total taken casual and annual leaves
        $leavePolicy = $employee->leavePolicy;
        $totalCasualTaken = $leaves->sum('taken_casual_leaves');
        $totalAnnualTaken = $leaves->sum('taken_annual_leaves');

        // Calculate the remaining leaves
        $remainingCasualLeaves = max(0, $leavePolicy->casual_leaves - $totalCasualTaken);
        $remainingAnnualLeaves = max(0, $leavePolicy->annual_leaves - $totalAnnualTaken);

        // Get the first leave record
        $firstLeaveRecord = $leaves->first();

        // Attach remaining leave data to the model
        $firstLeaveRecord->remaining_casual_leaves = $remainingCasualLeaves;
        $firstLeaveRecord->remaining_annual_leaves = $remainingAnnualLeaves;

        // Return the leave record
        return $firstLeaveRecord;
    }
}
