<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Room extends Authenticatable
{
    protected $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mtid', 'room', 'roomdetail'
    ];

}
