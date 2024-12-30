<?php

namespace App\Http\Controllers;

use App\Classes\ErrorResponse;
use App\Http\Requests\EmployeeLeaveRequest;
use App\Http\Resources\EmployeeLeaveResource;
use App\Http\Resources\SuccessResource;
use App\Http\Services\EmployeeLeaveService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeLeaveController extends Controller
{
    private EmployeeLeaveService $leaveService;

    public function __construct(EmployeeLeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function store(EmployeeLeaveRequest $request)
    {
        try {
            $data = $this->leaveService->store($request->validated());
            return new SuccessResource(['message' => 'Leave Stored Successfully!', 'data' => new EmployeeLeaveResource($data)]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function getById(int $id)
    {
        try {
            $data = $this->leaveService->getByEmployeeId($id);
            return new SuccessResource(['message' => 'Employee Leaves Retrieved Successfully!', 'data' => $data]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }
}
