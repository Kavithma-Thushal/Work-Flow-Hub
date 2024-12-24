<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'taken_casual_leaves' => $this->taken_casual_leaves,
            'taken_annual_leaves' => $this->taken_annual_leaves,
        ];
    }
}
