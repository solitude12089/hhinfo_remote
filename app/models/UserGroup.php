<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    //
  	protected $table = 'user_groups';
    public $timestamps = true;
    protected $guarded = ['id'];
}
