<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clss extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'deleted_at', 'created_at','updated_at'
    ];

    public function campus(){
        return $this->belongsTo('App\Campus', 'class_campus_id' , 'id');
    }

    public function students(){
        return $this->belongsToMany('App\User', 'class_users', 'class_id', 'user_id');
    }

}
