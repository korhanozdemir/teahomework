<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeworkAnswerSheet extends Model
{
    public function options(){
        return $this->hasMany('App\QuestionOption','question_id');
    }
}

