<?php

namespace App\Http\Services;

use App\Enums\HttpStatus;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    protected UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function register(array $data)
    {
        DB::beginTransaction();

        try {
            $user = $this->userRepositoryInterface->save([
                'name' => $data['name'] ?? null,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? null,
            ]);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'User registration failed');
        }
    }

    public function login(array $data): array
    {
        // Find user by email
        $user = $this->userRepositoryInterface->getByEmail($data['email']);

        // Check if user exists and password is valid
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpException(HttpStatus::UNPROCESSABLE_CONTENT, 'Invalid email or password');
        }

        // Generate access token
        $token = $user->createToken('auth-token')->accessToken;

        // Return user and token
        return ['user' => $user, 'access_token' => $token];
    }
}
