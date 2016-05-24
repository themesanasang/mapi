<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Users_net extends Authenticatable
{
    protected $table = 'users_net';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by', 'room', 'username', 'password', 'profile', 'created_type'
    ];

}
