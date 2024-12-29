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
            'company_id' => $this->company_id,
            'leave_policy_id' => $this->leave_policy_id,
            'name' => $this->name,
            'address' => $this->address,
            'salary' => $this->salary,
        ];
    }
}
