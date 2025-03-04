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
            return new SuccessResource([
                'message' => 'Leave Stored Successfully!',
                'leave' => new EmployeeLeaveResource($data)
            ]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function getById(int $id)
    {
        try {
            $data = $this->leaveService->getById($id);
            return new SuccessResource([
                'message' => 'Employee Leaves Retrieved Successfully!',
                'leave' => $data
            ]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function getAll()
    {
        try {
            $data = $this->leaveService->getAll();
            return new SuccessResource([
                'message' => 'Employee Leave Data Retrieved Successfully!',
                'leave' => $data
            ]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }
}
