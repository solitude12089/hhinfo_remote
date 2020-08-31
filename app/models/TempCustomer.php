<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TempCustomer extends Model
{
    protected $table = 'temp_customers';
    public $timestamps = true;
    protected $guarded = ['id'];
}
