<?php

namespace App\Http\Requests;

use App\Classes\ErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class EmployeeUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|string|max:500',
            'salary' => 'required|numeric|min:0',
            'leave_policy_id' => 'required|exists:leave_policies,id',
        ];
    }
}
