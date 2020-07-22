<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\User;
use App\Campus;
use App\Clss;
use App\Lecture;
use App\Course;
use App\Homework;
use App\Question;
use App\HomeworkStudentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(){
        return Auth::user()->getActiveHomework();
    }

    public function getStudentDetails(){
        return Auth::user()->getStudentUserDetails();
    }

    public function getHomework($id){
        $homeW =  Homework::find($id);

        if($homeW && $homeW->accessThisHomework()){
            $homework = Homework::with('questions')
                ->with('questions.images','questions.dropTargets','questions.options','questions.information','questions.targetMatching')
                ->with('answers')
                ->with('answers.options')

                ->find($id);

            $answers = HomeworkStudentResult::where('student_id', Auth::user()->id)
                ->where('homework_id', $homework->id)
                ->get();

            $homework['student_answers'] = $answers;

            return response()->json($homework, 200);


        }else {
            abort(403);
        }
    }


}
