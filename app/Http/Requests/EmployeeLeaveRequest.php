<?php

namespace App\Http\Requests;

use App\Classes\ErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class EmployeeLeaveRequest extends FormRequest
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
            'leave_type_id' => 'required|exists:leave_types,id',
            'date' => 'required|date|after_or_equal:today',
        ];
    }
}
