<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
	protected $table = 'devices';
    public $timestamps = true;
    protected $guarded = ['id'];

    public function converterList()
    {
        return $this->hasMany('\App\models\Converter', 'device_id', 'id');
    }

    public function BookingHistory()
    {
        return $this->hasMany('\App\models\BookingHistory', 'device_id', 'id');
    }
}
