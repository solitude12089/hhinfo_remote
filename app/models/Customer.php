<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $casts = [
        'groups' => 'array'
    ];

    public function cardList()
    {
        return $this->hasMany('\App\models\Card', 'customer_id', 'id');
    }

    public function last_update_user()
    {
        return $this->hasOne('\App\User', 'id', 'user_id');
    }

    public function bookingCustomer(){
        return $this->hasMany('\App\models\BookingCustomer', 'customer_id', 'id');
    }

   

    
}
