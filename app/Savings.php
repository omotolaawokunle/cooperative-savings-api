<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Savings extends Model
{
    protected $fillable = ['user_id', 'group_id', 'amount'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
