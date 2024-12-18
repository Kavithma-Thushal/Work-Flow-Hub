<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the role_manages for the role.
     */
    public function roleManages()
    {
        return $this->hasMany(RoleManage::class);
    }
}
