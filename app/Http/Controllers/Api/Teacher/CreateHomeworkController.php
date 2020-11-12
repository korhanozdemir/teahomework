<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Homework;
use App\HomeworkUser;
use App\Http\Controllers\Controller;
use App\Information;
use App\Question;
use App\QuestionDropTarget;
use App\QuestionImage;
use App\QuestionOption;
use App\TargetMatching;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CreateHomeworkController extends Controller
{
    public function index(Request $request){

        $validationRules = [
            'homework_name' => 'required',
            'homework_course_id' => 'required|numeric',
            'homework_lecture_id' => 'required|numeric',
            'homework_time' => 'required|numeric',
            'is_visible_before_deadline' => 'required|numeric',
            'questions' => 'array|required',
            'questions.*.question_type' => 'required|numeric',
            'questions.*.question_index' => 'required|numeric',
            'questions.*.answer' => 'required|numeric',
            'questions.*.correct_answer' => 'required|numeric',
            'questions.*.rating' => 'required|numeric',
            'questions.*.ratable' => 'required|numeric',
            'questions.*.is_question_pool' => 'required|numeric',
            'questions.*.images' => 'required',
            'questions.*.images.*.image_url' => 'required',
        ];
        $validator = Validator::make($request->all(),$validationRules);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ]);
        }

        /*

        $questionCorrectAnswers = "";

        foreach ($request->questions as $qreq) {

            $questionCorrectAnswers .= "question type: " . $qreq['question_type'] .
                "\nquestion index: " . $qreq['question_index'] .
                "\nquestion correct Answer: " . $qreq['correct_answer'] .
                "\n";

        }

        return $questionCorrectAnswers;
        //die();
        */

        $homework = new Homework();
        $homework -> homework_publisher_id = Auth::user()->id;
        $homework -> homework_name = $request->homework_name;
        $homework -> homework_course_id = $request->homework_course_id;
        $homework -> homework_lecture_id = $request->homework_lecture_id;
        $homework -> homework_time = $request->homework_time;
        $homework -> is_visible_before_deadline = $request->is_visible_before_deadline;
        $homework->save();

        $questArr = [];
        foreach ($request->questions as $qreq){
            $question = new Question();
            $question -> question_type = $qreq['question_type'];
            $question -> question_index = $qreq['question_index'];
            $question -> option_count = $qreq['option_count'];
            $question -> answer = $qreq['answer'];
            $question -> correct_answer = $qreq['correct_answer'];
            $question -> rating = $qreq['rating'];
            $question -> ratable = $qreq['ratable'];
            $question -> is_question_pool = $qreq['is_question_pool'];

            $homework->questions()->save($question);
            foreach ($qreq['images'] as $qimg){
                $image = new QuestionImage();
                $image -> image_url = $qimg['image_url'];
                $image -> bounds = $qimg['bounds'];
                $question->images()->save($image);
            }

            $qoptid= [];
            foreach ($qreq['options'] as $qopt){
                $qoptObj = new QuestionOption();
                $qoptObj -> option_text = $qopt['option_text'];
                $question->options()->save($qoptObj);
                $qoptid[] = $qoptObj->id;
            }


            foreach ($qreq['drop_targets'] as $qdrop){
                $qdropObj = new QuestionDropTarget();
                $qdropObj -> bounds = $qdrop['bounds'];
                $qdropObj -> anchor = $qdrop['anchor'];
                $qdropObj -> checked = $qdrop['checked'];
                $qdropObj -> correct_option_id = $qoptid[$qdrop['correct_option_id']];
                $question->dropTargets()->save($qdropObj);
            }




            foreach ($qreq['information'] as $qinf){
                $qinfObj = new Information();
                $qinfObj -> inf_type = $qinf['inf_type'];
                $qinfObj -> media_type = $qinf['media_type'];
                $qinfObj -> inf_url = $qinf['inf_url'];
                $qinfObj -> bounds = $qinf['bounds'];
                $question->information()->save($qinfObj);
            }


            foreach ($qreq['target_matching'] as $qtarget){
                $qtargetObj = new TargetMatching();
                $qtargetObj -> source_option_id = $qoptid[$qtarget['source_option_id']];
                $qtargetObj -> destination_option_id = $qoptid[$qtarget['destination_option_id']];
                $question -> targetMatching()->save($qtargetObj);
            }


        }
        $homework->students()->attach($request->students);

        return Homework::with('questions')
            ->with('questions.images')
            ->with('questions.dropTargets')
            ->with('questions.options')
            ->with('questions.information')
            ->with('questions.targetMatching')
            ->with('students')
            ->find($homework->id);


        $homework->questions()->delete();
        $homework->delete();

    }
}
