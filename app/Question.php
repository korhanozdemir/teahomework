<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $hidden = [
        'correct_answer', 'created_at', 'updated_at'
    ];

    public function getCorrectAnswer()
    {
        return $this->correct_answer;
    }

    public function images()
    {
        return $this->hasMany('App\QuestionImage','question_id');
    }

    public function dropTargets()
    {
        return $this->hasMany('App\QuestionDropTarget','question_id');
    }

    public function options()
    {
        return $this->hasMany('App\QuestionOption','question_id');
    }

    public function information()
    {
        return $this->hasMany('App\Information','question_id');
    }

    public function targetMatching(){
        return $this->hasMany('App\TargetMatching','question_id');
    }

    public function getAnswer(){
        $this->question_type;
        $a['question_id'] = $this->id;

        switch ($this->question_type) {
            case 0:
                $a["correct_answer"] =$this->getCorrectAnswer();
                $a["correct_answer"] =$this->getCorrectAnswer();
                break;
            case 1:
            case 2:
            case 4:
             $a['drop_targets'] =$this->dropTargets;
                break;
            case 3:
                $a['target_matchings'] = $this->targetMatching;
                break;
        }
        return $a;
    }
}
