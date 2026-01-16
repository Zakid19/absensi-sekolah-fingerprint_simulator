<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
