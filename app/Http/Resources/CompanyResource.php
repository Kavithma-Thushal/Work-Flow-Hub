<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'registration_no' => $this->registration_no,
            'address' => $this->address,
            'mobile' => $this->mobile,
        ];
    }
}
