<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class BookingCustomer extends Model
{
    protected $table = 'booking_customers';
    public $timestamps = true;
    protected $guarded = ['id'];


    public function customer()
    {
        return $this->hasOne('\App\models\Customer', 'id', 'customer_id');
    }

    public function bookingHistory(){
        return $this->hasOne('\App\models\BookingHistory','id','booking_id');
    }
}
