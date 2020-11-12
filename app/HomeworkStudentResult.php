<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeworkStudentResult extends Model
{
    protected $guarded = ['id', "correct_answer"];

    protected $hidden = ['created_at', 'updated_at','success_percent'];


    public function getSuccessPercent(){
        return $this->success_percent;
    }

    public function getTeacherID(){
        return Homework::find($this->homework_id)->teacher->id;
    }





}
