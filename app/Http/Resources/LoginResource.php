<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;

        $role_details = null;
        if ($user->hasRole('Company')) {
            $role_details = new CompanyResource($user->company);
        } elseif ($user->hasRole('Employee')) {
            $role_details = new EmployeeResource($user->employee);
        }

        return [
            'user' => new UserResource($this->user),
            'role_details' => $role_details,
            'access_token' => $this->access_token,
        ];
    }
}
