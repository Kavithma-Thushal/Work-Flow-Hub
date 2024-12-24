<?php

namespace App\Http\Requests;

use App\Classes\ErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class LeaveRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        ErrorResponse::validationError($validator);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'annual_leaves' => 'required|numeric|min:0',
            'casual_leaves' => 'required|numeric|min:0',
        ];
    }
}
