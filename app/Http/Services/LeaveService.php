<?php

namespace App\Http\Services;

use App\Models\EmployeeLeave;
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

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $leave = EmployeeLeave::create($data);
            DB::commit();
            return $leave;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Leave store failed: ' . $e->getMessage());
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
