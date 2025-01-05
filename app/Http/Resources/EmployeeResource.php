<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'leave_policy_id' => $this->leave_policy_id,
            'address' => $this->address,
            'salary' => $this->salary,
            'user' => new UserResource($this['user']),
        ];
    }
}
