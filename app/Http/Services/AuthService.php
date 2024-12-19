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
