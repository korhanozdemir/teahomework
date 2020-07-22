<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Clss;
use App\Course;
use App\Http\Controllers\Controller;
use App\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(){
        return json_encode("1");
    }

    public function getClassLevel(){
        return Clss::select("class_level")
            ->groupBy('class_level')
            ->get();
    }

    public function getCourseList($classID){
        return Course::where("course_level", $classID)->get();
    }


    public function getLectureList($course){
        return Lecture::where("parent_lecture_id", 0)
            ->where("lecture_course_id", $course)
            ->get();
    }

    public function getSubLectureList($lecture){
        return Lecture::where("parent_lecture_id", $lecture)
            ->where("parent_lecture_id", "!=", 0) // ERTAN: parent_id'si 0'dan farklı olanları getirmesi gerekiyor. Sadece alt konuları almak istiyoruz.
            ->get();
    }

    public function getStudentList($level){
        return Clss::where("class_level", $level)
            ->where("class_campus_id", 1)
            ->with("students")
            ->get();
    }

}
