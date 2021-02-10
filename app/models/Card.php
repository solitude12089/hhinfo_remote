<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = 'cards';
    public $timestamps = true;
    protected $guarded = ['id'];
    public function customer()
    {
        return $this->hasOne('\App\models\Customer', 'id', 'customer_id');
    }
}
