<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TimeRange extends Model
{
    protected $table = 'time_ranges';
    public $timestamps = true;
    protected $guarded = ['id'];
}
