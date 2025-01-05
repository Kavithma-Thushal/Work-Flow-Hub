<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function employee()
    {
        return $this->hasMany(Employee::class);
    }

    public function policyHasType()
    {
        return $this->hasMany(PolicyHasType::class);
    }
}
