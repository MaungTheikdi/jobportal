<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class BetUser extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasApiTokens;
    // use HasFactory;
    // //user_id	username	password_hash	balance	created_at
    // protected $table = 'bet_users';
    // protected $primaryKey = 'user_id';
    // public $timestamps = false;
    // protected $fillable = [
    //     'username',
    //     'password_hash',
    //     'balance',
    //     'created_at',
    // ];
    protected $table = 'bet_users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    protected $fillable = [
        'username',
        'email',
        'password',
        'password_hash',
        'balance',
        'created_at',
        'api_token',
        'remember_token'
    ];

    protected $hidden = [
        'password_hash',
        'password',
    ];

    public function getAuthPassword()
    {
        return $this->password;// or $this->password_hash;
    }

    // public function getAuthIdentifierName()
    // {
    //     return 'username';
    // }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }
}
