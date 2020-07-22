<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HomeworkStudentResult;
use Illuminate\Support\Facades\Auth;

class HomeworkStudentResultController extends Controller
{

    public function updateQuestionResults(Request $request){

        $homework_id =  $request->route('homework_id');
        $question_id =  $request->route('question_id');

        $input = $request->all();

        HomeworkStudentResult::where('homework_id', $homework_id)
            ->where('question_id', $question_id)
            ->delete();

        $allAnswers = [];

        foreach ($input as $ans){
            $ans['student_id'] = Auth::user()->id;
            $ans['homework_id'] = $homework_id;
            $ans['question_id'] = $question_id;
            $allAnswers[] = HomeworkStudentResult::create($ans);
        }

        return response()->json( $allAnswers , 200);

    }

    public function getQuestionResults(Request $request){

        $homework_id =  $request->route('homework_id');
        $question_id =  $request->route('question_id');

        $allAnswers = HomeworkStudentResult::where('homework_id',$homework_id)
            ->where('question_id',$question_id)->get();



        return response()->json( $allAnswers , 200);

    }
}
