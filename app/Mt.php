<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Mt extends Authenticatable
{
    protected $table = 'routes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mtname', 'mtip', 'mtport', 'mtusername', 'mtpassword','mtdetail','usermanage'
    ];

}
