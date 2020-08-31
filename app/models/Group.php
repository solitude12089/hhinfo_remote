<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function userGroupList()
    {
        return $this->hasMany('\App\models\UserGroup', 'group_id', 'id');
    }
}
