<?php

namespace App\Http\Controllers;

use App\Classes\ErrorResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\LoginResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\UserResource;
use App\Http\Services\AuthService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $data = $this->authService->register($request->validated());
            return new SuccessResource([
                'message' => 'Company Registered Successfully!',
                'user' => new UserResource($data['user']),
                'company' => new CompanyResource($data['company']),
            ]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->validated());
            return new SuccessResource([
                'message' => 'User Logged in Successfully!',
                'data' => new LoginResource((object)$data)
            ]);
        } catch (HttpException $e) {
            ErrorResponse::throwException($e);
        }
    }
}
