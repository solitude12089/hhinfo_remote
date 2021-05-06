<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    //
  	protected $table = 'user_groups';
    public $timestamps = true;
    protected $guarded = ['id'];

    public function group()
    {
        return $this->hasOne('\App\models\Group', 'id', 'group_id');
    }

}
