<?php

namespace App\Http\Services;

use App\Enums\HttpStatus;
use App\Models\Company;
use App\Repositories\Company\CompanyRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    protected UserRepositoryInterface $userRepositoryInterface;
    protected CompanyRepositoryInterface $companyRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, CompanyRepositoryInterface $companyRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->companyRepositoryInterface = $companyRepositoryInterface;
    }

    public function register(array $data)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->save([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('company');

            $this->companyRepositoryInterface->save([
                'user_id' => $user->id,
                'registration_no' => $data['registration_no'],
                'address' => $data['address'],
                'mobile' => $data['mobile'],
            ]);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'User registration failed: ' . $e->getMessage());
        }
    }

    public function login(array $data): array
    {
        $user = $this->userRepositoryInterface->getByEmail($data['email']);

        // If user not found, throw an HttpException
        if (!$user) {
            throw new HttpException(HttpStatus::UNPROCESSABLE_CONTENT, 'Username or password invalid');
        }

        // Check if the provided password matches the hashed password
        if (!Hash::check($data['password'], $user->password)) {
            throw new HttpException(HttpStatus::UNPROCESSABLE_CONTENT, 'Username or password invalid');
        }

        // Create a personal access token
        $token = $user->createToken('work-flow-hub')->accessToken;

        // If token creation fails, throw an error
        if (!$token) {
            throw new HttpException(HttpStatus::INTERNAL_SERVER_ERROR, 'User authentication failed');
        }

        return ['user' => $user, 'access_token' => $token];
    }
}
