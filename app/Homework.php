<?php

namespace App;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Homework extends Model
{
    public $timestamps = true;

    public function course(){
        return $this->belongsTo('App\Course', 'homework_course_id' , 'id');
    }

    public function students(){
        return $this->belongsToMany('App\User', 'homework_users', 'homework_id', 'user_id');
    }

    public function questions(){
        return $this->hasMany('App\Question');
    }

    public function answers(){
        return $this->hasMany('App\HomeworkAnswerSheet', 'homework_id', 'id');
    }


    public function accessThisHomework(){
        return HomeworkUser::where('homework_id', $this->id)
            ->where('user_id', Auth::user()->id)
            ->where('start_date', '<=', Carbon::now())
            ->where('deadline', '>=', Carbon::now())
            ->count();
    }
}
