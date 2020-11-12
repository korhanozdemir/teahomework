<?php

namespace App\Http\Controllers\Api\Student;

use App\Homework;
use App\HomeworkUser;
use App\Http\Controllers\Calculate\CalculateController;
use App\Http\Controllers\Controller;
use App\Question;
use App\Setting;
use App\StudentQuestionAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\HomeworkStudentResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function App\Http\Controllers\Media\generateRandomString;
use Validator;

class HomeworkStudentResultController extends CalculateController
{
    public function generateRandomString() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        $length = rand(20,60);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function updateQuestionResults(Request $request,$homework_id,$question_id){

        $typeArr =  Question::where("id", $question_id)
            ->pluck("question_type");
        $type = count($typeArr) ? $typeArr[0] : 0;
        $cnt = HomeworkUser::where("homework_id",$homework_id)
            ->where("user_id", Auth::user()->id)
            ->whereNull("completed_date")
            ->count();

        if($cnt==0){
            abort(400, "Tamamladığınız ödevin cevaplarını değiştiremezsiniz!");
        }

        $input = $request->all();
        $userid = Auth::user()->id;
        $indexZ = Question::where("id",$question_id)->pluck('question_index');
        if (count($indexZ)){
            $index = $indexZ[0];
        }

        $audio_res = HomeworkStudentResult::where('homework_id', $homework_id)
            ->where('question_id', $question_id)
            ->where('student_id', $userid)
            ->where('type', 5)
            ->get();
        $audio = null;
        foreach ($audio_res as $res){
            if(isset($res->audio)){
                $audio = $res->audio;
                break;
            }
        }

        if( ! (isset($input[0]['done']) && $input[0]['done'] ==1) ){
            HomeworkStudentResult::where('homework_id', $homework_id)
                ->where('question_id', $question_id)
                ->where('student_id', $userid)
                ->delete();
        }

        $allAnswers = [];
        foreach ($input as $ans){
            $ans['student_id'] = $userid;
            $ans['homework_id'] = $homework_id;
            $ans['question_id'] = $question_id;
            $ans['type'] = $type;
            $ans['question_index'] = isset($index) ? $index : $ans['question_index'];
            $ans['audio'] = $audio;
            print_r( $ans['audio']);
            print_r( $audio);
            if(isset($ans['done']) && $ans['done'] == 1 ){
                $this->calculate($homework_id);
                HomeworkUser::where("user_id", $userid)->where("homework_id",$homework_id)->update(['completed_date'=>date("Y-m-d H:i:s")]);
                unset($ans['done']);
                return response()->json( 1 , 200);
            }else {
                $allAnswers[] = HomeworkStudentResult::create($ans);
            }
        }

        return response()->json( $allAnswers , 200);

    }

    public function updateScreeShot(Request $request,$homework_id,$question_id){

        $validationRules = [
            'screenshot' => 'required'
        ];

        $validator = Validator::make($request->all(),$validationRules);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ],400);
        }


        $image = $request->all("screenshot");


        HomeworkStudentResult::where("homework_id",$homework_id)
            ->where("question_id",$question_id)
            ->where("student_id", Auth::user()->id)
            ->first()
            ->update($image);

        return response()->json([
        ],200);


    }



    public function updateAudio(Request $request,$homework_id,$question_id){

        $validationRules = [
            'audio' => 'required'
        ];

        $validator = Validator::make($request->all(),$validationRules);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ],400);
        }

        $audio= $request->all("audio");
        $userid = Auth::user()->id;
        $typeArr =  Question::where("id", $question_id)
            ->pluck("question_type");
        $type = count($typeArr) ? $typeArr[0] : 0;
        $indexZ = Question::where("id",$question_id)->pluck('question_index');
        if (count($indexZ)){
            $index = $indexZ[0];
        }
        $ans['student_id'] = $userid;
        $ans['homework_id'] = $homework_id;
        $ans['question_id'] = $question_id;
        $ans['type'] = $type;
        $ans['question_index'] = isset($index) ? $index : $ans['question_index'];
        $ans['audio'] = $audio["audio"];

         HomeworkStudentResult::create($ans);

        return response()->json([
        ],200);


    }



    public function getQuestionResults(Request $request){
        $homework_id =  $request->route('homework_id');
        $question_id =  $request->route('question_id');
        $allAnswers = HomeworkStudentResult::where('homework_id',$homework_id)
            ->where("student_id", Auth::user()->id)
            ->where('question_id',$question_id)->get()
            ->makeHidden('screenshot','audio');
        return response()->json( $allAnswers , 200);
    }



    public function getQuizAnswerSheets($homework_id){
        $homework = Homework::find($homework_id)->with("questions")->find($homework_id);
        if( !$homework->isWisibleAnswer() ){
            abort(403,"Yetkisiz erişim");
        }
        $questions =  $homework->questions;
        $answers = [];
        foreach ($questions as $index => $qu){
            $answers[$index] = $qu->getAnswer();
        }
        return response()->json( $answers , 200);
    }

    public function calCulateUnfinishedHomeworks(){

        $calcs =  HomeworkUser::select("homework_users.homework_id", "homework_users.user_id", "homework.homework_time")
            ->leftJoin("homework", "homework.id", "=", "homework_users.homework_id")
            ->whereNull("completed_date")
            ->whereNotNull("started_at")
            ->where("homework_time", "!=", 0)
            ->whereraw("DATE_ADD(started_at, INTERVAL homework_time second) < now()")
            ->get();



        if(count($calcs)==0){
            return 1;
        }

        foreach ($calcs as $calc) {
            $this->calculate($calc->homework_id,$calc->user_id);

        }

        DB::select(DB::raw("Update homework_users as hus,homework as hw
            SET completed_date = DATE_ADD(started_at, INTERVAL total_time SECOND)
            where hw.id=hus.homework_id
            AND hus.completed_date is null
            AND hus.started_at is not null
            AND DATE_ADD(started_at, INTERVAL hw.homework_time second) < now()"));

        return 1;
    }









}
