<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

//    public function relationships(){
//        return $this -> belongsToMany('App\Relation');
//    }

    public function friends(){
        return $this->belongsToMany('App\User', 'user_users','user_id', 'friend_id');
    }
    public function users(){
        return $this->belongsToMany('App\User', 'user_users','friend_id', 'user_id');
    }
}
