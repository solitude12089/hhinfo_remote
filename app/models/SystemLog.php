<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_logs';
    public $timestamps = true;
    protected $guarded = ['id'];


    public function user()
    {
        return $this->hasOne('\App\User', 'id', 'user_id');
    }
    public function customer()
    {
        return $this->hasOne('\App\models\Customer', 'id', 'user_id');
    }

    public function device()
    {
        return $this->hasOne('\App\models\Device', 'id', 'col1');
    }


   
}
