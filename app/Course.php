<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{

    protected $hidden = [
        'deleted_at', 'created_at','updated_at'
    ];


    public function lectures(){
        return $this->hasMany('App\Lecture', 'lecture_course_id');
    }


    public function homeworks(){
        return $this->hasMany('App\Homework', 'homework_course_id');
    }



}
