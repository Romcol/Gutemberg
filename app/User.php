<?php

namespace App;

use Moloquent;
use Fadion\Bouncy\BouncyTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class User extends Moloquent implements Authenticatable
{
    use AuthenticableTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    use BouncyTrait;
    public $timestamps  = false;
    protected $collection = 'Users';
    protected $indexName = 'users';
    protected $typeName = 'account';
}

?>