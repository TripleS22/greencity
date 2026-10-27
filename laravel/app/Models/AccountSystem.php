<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountSystem extends Model
{
    protected $fillable = ['email', 'password', 'platform_url'];

    protected $hidden = ['password'];
}
