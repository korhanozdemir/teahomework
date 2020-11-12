<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    protected $hidden = [
        'deleted_at', 'created_at','updated_at'
    ];

    public function course(){
        return $this->belongsTo('App\Course', 'lecture_course_id' , 'id');
    }

    public function parentlecture(){
        return $this->belongsTo('App\Lecture', 'parent_lecture_id' , 'id')->select(array('id', 'lecture_name','parent_lecture_id'));
    }
}
