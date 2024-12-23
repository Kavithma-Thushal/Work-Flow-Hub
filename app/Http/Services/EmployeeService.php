<?php

namespace App\Http\Services;

use App\Repositories\Leave\LeaveRepositoryInterface;
use App\Repositories\LeavePolicy\LeavePolicyRepositoryInterface;
use Exception;
use App\Enums\HttpStatus;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeService
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
            $employee = $this->employeeRepositoryInterface->store([
                'user_id' => Auth::user()->id,
                'company_id' => Auth::user()->id,
                'name' => $data['name'],
                'address' => $data['address'],
                'salary' => $data['salary'],
            ]);

            $leavePolicies = $this->leavePolicyRepositoryInterface->getAll();
            foreach ($leavePolicies as $leavePolicy) {
                $this->leaveRepositoryInterface->store([
                    'employee_id' => $employee->id,
                    'leave_policy_id' => $leavePolicy->id,
                    'taken_casual_leaves' => 0,
                    'taken_annual_leaves' => 0,
                ]);
            }

            DB::commit();
            return $employee;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Employee save failed: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $employee = $this->employeeRepositoryInterface->update($id, [
                'name' => $data['name'] ?? null,
                'address' => $data['address'] ?? null,
                'salary' => $data['salary'] ?? null,
            ]);

            if (!$employee) {
                throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
            }

            DB::commit();
            return $employee;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Employee update failed: ' . $e->getMessage());
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();
        try {

            $employee = $this->employeeRepositoryInterface->getById($id);

            if (!$employee) {
                throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
            }

            $this->employeeRepositoryInterface->delete($id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Employee delete failed: ' . $e->getMessage());
        }
    }

    public function getById(int $id)
    {
        $employee = $this->employeeRepositoryInterface->getById($id);

        if (!$employee) {
            throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
        }

        return $employee;
    }

    public function getAll()
    {
        return $this->employeeRepositoryInterface->getAll();
    }
}
