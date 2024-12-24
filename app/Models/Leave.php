<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'taken_casual_leaves',
        'taken_annual_leaves',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
