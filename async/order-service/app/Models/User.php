<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'user_mysql';
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password'
    ];
}
