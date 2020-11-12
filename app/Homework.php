<?php

namespace App;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Homework extends Model
{
    public $timestamps = true;
    use SoftDeletes;



    public function students(){
        return $this->belongsToMany('App\User', 'homework_users', 'homework_id', 'user_id');
    }
    public function getdeadline(){
        return $this->belongsToMany('App\User', 'homework_users', 'homework_id', 'user_id')->select(array('deadline'));;
    }

    public function questions(){
        return $this->hasMany('App\Question');
    }

    public function answers(){
        return $this->hasMany('App\HomeworkAnswerSheet', 'homework_id', 'id');
    }

    public function teacher(){
        return $this->belongsTo('App\User', 'homework_publisher_id' , 'id')->select(array('id', 'name'));
    }

    public function course(){
        return $this->belongsTo('App\Course', 'homework_course_id' , 'id')->select(array('id', 'course_name','course_level'));
    }
    public function lecture(){
        return $this->belongsTo('App\Lecture', 'homework_lecture_id' , 'id')->select(array('id', 'lecture_name','parent_lecture_id'));
    }

    public function getParentLecture(){
        if(isset($this->lecture['parent_lecture_id']) && $this->lecture['parent_lecture_id'] >0){
            return Lecture::where("id", $this->lecture['parent_lecture_id'])->first();
        }else {
            return null;
        }

    }



    public function accessThisHomework(){
        return HomeworkUser::where('homework_id', $this->id)
            ->where('user_id', Auth::user()->id)
            ->where('start_date', '<=', Carbon::now())
            ->count();
    }

    public function isWisibleAnswer(){
        $Huser = HomeworkUser::where('homework_id', $this->id)
            ->where('user_id', Auth::user()->id)
            ->where('start_date', '<=', Carbon::now())
            ->whereNotNull('completed_date');

        if($this->is_visible_before_deadline == 1){
            return $Huser->count();
        }else {
            return $Huser->where('deadline', '<', Carbon::now())->count();
        }
    }

    public function isCalculatedBefore($user_id = null){
        $user_id = $user_id ? $user_id : Auth::user()->id;
        return HomeworkUser::where("user_id", $user_id)
            ->where("homework_id",$this->id)
            ->whereNotNull('completed_date')
            ->count();
    }

    public function getDonePercent(){

        $all = HomeworkUser::where("homework_id", $this->id)
            ->count();

        $done = HomeworkUser::where("homework_id", $this->id)
            ->whereNotNull("completed_date")
            ->count();

        $percent = $done == 0 ? 0 : $done/$all*100;

        return $percent;

    }

    public function getPointAvg(){
        $cnt = HomeworkUser::where("homework_id", $this->id)
            ->whereNotNull("completed_date")
            ->count();

        $total = HomeworkUser::where("homework_id", $this->id)
            ->whereNotNull("completed_date")
            ->sum("point");
        $avg = $cnt > 0 ? $total/$cnt : 0;

        return $avg;



    }



}
