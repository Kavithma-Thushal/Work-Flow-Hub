<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'address',
        'salary',
    ];

    /**
     * Get the user that owns the employee.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the employee.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }
}
