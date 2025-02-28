<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['nama_departemen'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}


