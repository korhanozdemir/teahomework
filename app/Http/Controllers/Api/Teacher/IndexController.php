<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Clss;
use App\Course;
use App\Homework;
use App\HomeworkUser;
use App\Http\Controllers\Controller;
use App\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

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

    public function getAllCourseList(){
        return Course::all();
    }

    public function getSingleCourse($courseID) {
        return Course::where("id", $courseID)->get();
    }

    public function getLectureList($course){
        return Lecture::where("parent_lecture_id", 0)
            ->where("lecture_course_id", $course)
            ->get();
    }

    public function getSingleLecture($lectureID){
        return Lecture::where("id", $lectureID)
            ->get();
    }

    public function getSubLectureList($lecture){
        return Lecture::where("parent_lecture_id", $lecture)
            ->where("parent_lecture_id", "!=", 0)
            ->get();
    }

    public function getStudentList($level){
        /*
         * return Clss::where("class_level", $level)
            ->where("class_campus_id", 1)
            ->with("students")
            ->get();
        */

		/*
         $test = Clss::where("class_level", 99)
             ->count();

         if($test){
             return Clss::where("class_level", $level)
                 ->where("class_campus_id", 1)
                 ->with("students")
                 ->get();
         }


        return Clss::where("class_level", $level)
            ->where("class_campus_id", 1)
            ->with("students")
            ->get();
            
            */
            
        // MODIFIED BY ERTAN:
        
        return Clss::where("class_level", $level)
            ->orWhere("class_level", 100) // ERTAN: class_level 100 olanların getirilmesi.
            ->where("class_campus_id", 1)
            ->with("students")
            ->get();
            
    }


    public function getAllHomework() {
        if(Auth::user()->hasRole("admin")){
            return Homework::join("courses", "courses.id", "=", "homework.homework_course_id")
                ->join("homework_users", "homework_id", "=", "homework.id")
                ->get(["homework.*", "courses.course_name", "homework_users.deadline"]);
        } else if(Auth::user()->hasRole("teacher")){
            return Homework::where("homework_publisher_id", Auth::user()->id)
                ->join("courses", "courses.id", "=", "homework.homework_course_id")
                ->join("homework_users", "homework_id", "=", "homework.id")
                ->get(["homework.*", "courses.course_name", "homework_users.deadline"]);
        }
    }

    public function updateHomework($homeworkID,Request $request)
    {
        $validationRules = [
            'deadline' => 'required|date_format:Y-m-d H:i:s|after:1 seconds'
        ];
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        $homework = Homework::where("id", $homeworkID)
            ->where("homework_publisher_id", Auth::user()->id)
            ->count();
        if (!$homework) {
            abort(403, "Sadece kendi ödevlerinizi düzenleyebilirsiniz.");
        }
        $updated = HomeworkUser::where("homework_id", $homeworkID)
            ->update(["deadline" => $request->deadline]);
        if (!$updated) {
            abort(500, "Bir hata oluştu");
        }
        return response("Düzenleme Başarılı", 200);
    }


    public function deleteHomework($homeworkID)
    {
        $homework =  Homework::where("id", $homeworkID)
            ->where("homework_publisher_id", Auth::user()->id)
            ->count();
        if(!$homework) {
            abort(403,"Sadece kendi ödevlerinizi silebilirsiniz.");
        }
        $hUser =  HomeworkUser::where("homework_id",$homeworkID)
           ->delete();
        $homeworkD = Homework::where("id", $homeworkID)
            ->delete();

        if(!$homeworkD) {
            abort(500,"Bir hata oluştu");
        }

        return response(['message' => "Silme Başarılı"], 200);
    }

}
