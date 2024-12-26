<?php

namespace App\Http\Controllers;

use App\Classes\ErrorResponse;
use App\Http\Requests\LeaveRequest;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\SuccessResource;
use App\Http\Services\LeaveService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LeaveController extends Controller
{
    private LeaveService $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function store(LeaveRequest $request)
    {
        try {
            $data = $this->leaveService->store($request->validated());
            return new SuccessResource(['message' => 'Leave Stored Successfully!', 'data' => new LeaveResource($data)]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function getById(int $id)
    {
        try {
            $data = $this->leaveService->getByEmployeeId($id);
            return new SuccessResource(['message' => 'Employee Leaves Retrieved Successfully!', 'data' => LeaveResource::collection($data)]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }
}
