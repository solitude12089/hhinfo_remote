<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class BookingHistory extends Model
{
  	protected $table = 'booking_histories';
    public $timestamps = true;
    protected $guarded = ['id'];


    public function customer()
    {
        return $this->hasOne('\App\models\Customer', 'id', 'customer_id');
    }
}
