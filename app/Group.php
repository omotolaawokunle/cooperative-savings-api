<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    protected $fillable = ['name', 'description', 'max_capacity', 'is_searchable', 'periodic_amount'];


    public function admin()
    {
        return $this->belongsTo(User::class, 'group_admin');
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->using('\App\GroupUser');
    }
    public function userCount($groupId)
    {
        $groups = DB::select(DB::raw("select group_id,count(group_id) AS count,group_id from
       group_user b where '$groupId'=b.group_id group by group_id"));
        if (!empty($groups)) {
            return $groups[0]->{'count'};
        } else {
            return 0;
        }
    }
    public function savings()
    {
        return $this->hasMany(Savings::class, 'group_id');
    }
    public function collection()
    {
        return $this->hasOne(Collection::class);
    }
}
