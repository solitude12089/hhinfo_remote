<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Spcard extends Model
{
    //
    protected $table = 'spcards';
    public $timestamps = true;
    protected $guarded = ['id'];

    protected $casts = [
        'family' => 'array',
    ];

    public function customer()
    {
        return $this->hasOne('\App\models\Customer', 'id', 'customer_id');
    }

    public function group()
    {
        return $this->hasOne('\App\models\Group', 'id', 'group_id');
    }


}
