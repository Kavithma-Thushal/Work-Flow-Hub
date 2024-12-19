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

    public function save(EmployeeRequest $request)
    {
        try {
            $data = $this->employeeService->save($request->validated());
            return new SuccessResource(['message' => 'Employee Saved Successfully!', 'data' => new EmployeeResource($data)]);
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
}
