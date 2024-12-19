<?php

namespace App\Http\Services;

use Exception;
use App\Enums\HttpStatus;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeService
{
    protected EmployeeRepositoryInterface $employeeRepositoryInterface;

    public function __construct(EmployeeRepositoryInterface $employeeRepositoryInterface)
    {
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $employee = $this->employeeRepositoryInterface->save([
                'company_id' => Auth::user()->id,
                'name' => $data['name'],
                'address' => $data['address'],
                'salary' => $data['salary'],
            ]);

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
}
