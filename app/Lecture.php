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
}
