<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    protected $fillable = [
        'is_read','created_at'
    ];

    public $timestamps = true;

    public function homework(){
        return $this->belongsTo('App\Homework', 'homework_id' , 'id');
    }

}
