<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'employee_id' => $this->employee_id,
            'leave_policy_id' => $this->leave_policy_id,
            'taken_casual_leaves' => $this->taken_casual_leaves,
            'taken_annual_leaves' => $this->taken_annual_leaves,
        ];
    }
}
