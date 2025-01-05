<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function employeeLeave()
    {
        return $this->hasMany(EmployeeLeave::class);
    }

    public function policyHasType()
    {
        return $this->hasMany(PolicyHasType::class);
    }
}
