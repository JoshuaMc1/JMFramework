<?php

namespace App\Models;

use Lib\Model\Model;

class User extends Model
{
    protected $table = 'users';

    protected $hidden = [
        'password',
    ];
}
