<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionPool extends Model
{
    use SoftDeletes;


    public function images(){
        return $this->hasMany('App\QuestionImage','question_id');
    }

    public function dropTargets(){
        return $this->hasMany('App\QuestionDropTarget','question_id');
    }

    public function options(){
        return $this->hasMany('App\QuestionOption','question_id');
    }

    public function information(){
        return $this->hasMany('App\Information','question_id');
    }

    public function targetMatching(){
        return $this->hasMany('App\TargetMatching','question_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'question_publisher_id' , 'id')->select(array('id', 'name'));
    }

    public function course(){
        return $this->belongsTo('App\Course', 'course' , 'id')->select(array('id', 'course_name'));
    }
    public function lecture(){
        return $this->belongsTo('App\Lecture', 'lecture' , 'id')->select(array('id', 'lecture_name'));
    }
    public function sublecture(){
        return $this->belongsTo('App\Lecture', 'sublecture' , 'id')->select(array('id', 'lecture_name'));
    }

}
