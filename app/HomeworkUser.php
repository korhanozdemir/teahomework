<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeworkUser extends Model
{
    public $timestamps = true;

    public function homework(){
        return $this->belongsTo('App\Homework');
    }

}
