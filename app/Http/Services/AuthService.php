<?php

namespace App\Http\Services;

use App\Enums\HttpStatus;
use App\Models\Company;
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
            if ($data['role'] === 'company') {
                $user = $this->userRepositoryInterface->save([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                ]);

                $user->assignRole('company');

                Company::create([
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $user->password,
                ]);
            } else {
                throw new HttpException(HttpStatus::BAD_REQUEST, 'Only companies can register');
            }
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'User registration failed: ' . $e->getMessage());
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

        // Check if the role is 'company'
        if ($user->hasRole('company')) {
            $token = $user->createToken('auth-token')->accessToken;
            return ['user' => $user, 'access_token' => $token];
        } else {
            return ['user' => $user, 'access_token' => 'No Access'];
        }
    }
}
