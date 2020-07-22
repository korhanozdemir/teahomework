<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Campus extends Model
{
    use SoftDeletes;

    public function classes(){
        return $this->hasMany('App\Clss','class_campus_id');
    }

}
