<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $hidden = [
        'correct_answer', 'created_at', 'updated_at'
    ];

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
}
