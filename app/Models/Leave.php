<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'leave_policy_id', 'taken_leaves', 'remaining_leaves'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leavePolicy()
    {
        return $this->belongsTo(LeavePolicy::class, 'leave_policy_id');
    }
}
