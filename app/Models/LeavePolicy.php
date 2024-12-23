<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'casual_leaves',
        'annual_leaves'
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}
