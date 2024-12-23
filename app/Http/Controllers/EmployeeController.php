<?php

namespace App\Http\Controllers;

use App\Classes\ErrorResponse;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\SuccessResource;
use App\Http\Services\EmployeeService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeController extends Controller
{
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function store(EmployeeRequest $request)
    {
        try {
            $data = $this->employeeService->store($request->validated());
            return new SuccessResource(['message' => 'Employee Stored Successfully!', 'data' => new EmployeeResource($data)]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function update(EmployeeRequest $request, int $id)
    {
        try {
            $data = $this->employeeService->update($id, $request->validated());
            return new SuccessResource(['message' => 'Employee Updated Successfully!', 'data' => new EmployeeResource($data)]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function delete(int $id)
    {
        try {
            $this->employeeService->delete($id);
            return new SuccessResource(['message' => 'Employee Deleted Successfully!']);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function getById(int $id)
    {
        try {
            $data = $this->employeeService->getById($id);
            return new SuccessResource(['message' => 'Employee Retrieved Successfully!', 'data' => new EmployeeResource($data)]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function getAll()
    {
        try {
            $data = $this->employeeService->getAll();
            return new SuccessResource(['message' => 'All Employees Retrieved Successfully!', 'data' => EmployeeResource::collection($data)]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }
}
