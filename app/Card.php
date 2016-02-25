<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Card extends Authenticatable
{
    protected $table = 'cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by', 'room', 'username', 'password', 'profile'
    ];

}
