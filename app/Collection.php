<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = ['list'];
    public function user()
    {
        return $this->belongsTo(Group::class);
    }
}
