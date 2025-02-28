<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // 1 Roles bisa dimiliki banyak user
    public function users()
    {
        return $this->hasMany(User::class, 'user_id');
    }
}
