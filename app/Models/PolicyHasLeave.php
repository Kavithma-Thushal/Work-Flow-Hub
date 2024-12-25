<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyHasLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_policy_id',
        'leave_type_id',
        'amount',
    ];

    public function leavePolicy()
    {
        return $this->belongsTo(LeavePolicy::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
