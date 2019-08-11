<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'address',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }
    public function group()
    {
        return $this->hasOne(Group::class, 'group_admin');
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class)->using('\App\GroupUser');
    }
    public function inGroup($id): bool
    {
        return (bool) $this->groups()->where('group_id', $id)->first();
    }
    public function savingsWithGroup($id)
    {
        $savings = $this->savings()->where('group_id', $id)->first();
        if (is_null($savings)) {
            return 0;
        } else {
            return $savings;
        }
    }
    public function getGroup($id)
    {
        return $this->groups()->where('group_id', $id)->first();
    }
    public function savings()
    {
        return $this->hasMany(Savings::class, 'user_id');
    }
}
