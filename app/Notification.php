<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    public $timestamps = true;


    public function users(){
        return $this->belongsToMany('App\User', 'notification_users', 'text_id', 'user_id')->withTimestamps();
    }


}
