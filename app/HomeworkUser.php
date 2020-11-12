<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeworkUser extends Model
{
    protected $fillable = [
        'teacher_comment'
    ];


    public $timestamps = true;

    public function homework(){
        return $this->belongsTo('App\Homework');
    }

    public function user(){
        return $this->belongsTo('App\User')->select(array('id', 'name'));
    }

}
