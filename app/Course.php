<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillables = ['name','description','chapters'];
    public function users()
    {
        return $this->belongsToMany(User::class)->using('\App\CourseUser');
    }
}
