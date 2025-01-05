<?php

namespace App\Http\Services;

use App\Repositories\Company\CompanyRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Exception;
use App\Enums\HttpStatus;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeService
{
    protected UserRepositoryInterface $userRepositoryInterface;
    protected CompanyRepositoryInterface $companyRepositoryInterface;
    protected EmployeeRepositoryInterface $employeeRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, CompanyRepositoryInterface $companyRepositoryInterface, EmployeeRepositoryInterface $employeeRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->companyRepositoryInterface = $companyRepositoryInterface;
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->store([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('Employee');

            $employee = $this->employeeRepositoryInterface->store([
                'user_id' => $user->id,
                'company_id' => Auth::user()->id,
                'address' => $data['address'],
                'salary' => $data['salary'],
                'leave_policy_id' => $data['leave_policy_id'],
            ]);

            DB::commit();
            return $employee;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Employee store failed: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            // Retrieve the authenticated user's ID
            $userId = Auth::id();

            // Retrieve the company record associated with the authenticated user
            $company = $this->companyRepositoryInterface->getByUserId($userId);

            // Retrieve the employee
            $employee = $this->employeeRepositoryInterface->getById($id);

            if (!$employee) {
                throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
            }

            // Verify the employee belongs to the authenticated user's company
            if ($employee->company_id !== $company->id) {
                throw new HttpException(HttpStatus::FORBIDDEN, 'Employee does not belong to your company.');
            }

            // Retrieve the user associated with the employee
            $user = $employee->user;

            $this->userRepositoryInterface->update($user->id, [
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);

            $employee = $this->employeeRepositoryInterface->update($id, [
                'address' => $data['address'] ?? null,
                'salary' => $data['salary'] ?? null,
                'leave_policy_id' => $data['leave_policy_id'] ?? null,
            ]);

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
            // Retrieve the authenticated user's ID
            $userId = Auth::id();

            // Retrieve the company record associated with the authenticated user
            $company = $this->companyRepositoryInterface->getByUserId($userId);

            // Retrieve the employee
            $employee = $this->employeeRepositoryInterface->getById($id);

            if (!$employee) {
                throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
            }

            // Verify the employee belongs to the authenticated user's company
            if ($employee->company_id !== $company->id) {
                throw new HttpException(HttpStatus::FORBIDDEN, 'Employee does not belong to your company.');
            }

            // Delete the employee record
            $this->employeeRepositoryInterface->delete($id);

            // Delete the user record associated with the employee
            $employee->user->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'Employee delete failed: ' . $e->getMessage());
        }
    }

    public function getById(int $id)
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::id();

        // Retrieve the company record associated with the authenticated user
        $company = $this->companyRepositoryInterface->getByUserId($userId);

        // Retrieve the employee
        $employee = $this->employeeRepositoryInterface->getById($id);

        if (!$employee) {
            throw new HttpException(HttpStatus::NOT_FOUND, 'Employee not found');
        }

        // Verify the employee belongs to the authenticated user's company
        if ($employee->company_id !== $company->id) {
            throw new HttpException(HttpStatus::FORBIDDEN, 'Employee does not belong to your company.');
        }

        return $employee;
    }

    public function getAll()
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::id();

        // Retrieve the company record associated with the authenticated user
        $company = $this->companyRepositoryInterface->getByUserId($userId);

        // Retrieve all employees belonging to the authenticated user's company
        return $this->employeeRepositoryInterface->getByCompanyId($company->id);
    }
}
