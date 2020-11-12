<?php

namespace App\Http\Controllers\Calculate;

use App\Homework;
use App\HomeworkStudentResult;
use App\HomeworkUser;
use App\Http\Controllers\Controller;
use App\Question;
use App\QuestionDropTarget;
use App\TotalPoint;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalculateController extends Controller
{
    public function calculate($homework_id, $userid=null){


        if(!$userid){
            $userid = Auth::user()->id;
        }

        if(!$userid){
            abort(401);
        }

        if(Homework::find($homework_id)->isCalculatedBefore($userid)){
            abort(401,"Yetkisiz Erişim. Ödev daha önce puanlanmış!");
        }


        $questions =  Question::where('homework_id', $homework_id)->get();


            foreach ($questions as $index => $question){

            $result =  HomeworkStudentResult::where('homework_id', $homework_id)
                ->where('student_id', $userid)
                ->where("question_index", $question->question_index)->get();



                if( !count($result) ){
                continue;
            }

                switch ($question->question_type) {
                case 0:
                    $this->calcType0($question,$result->first());
                    break;
                case 1:
                case 2:
                    $this->calcType1($question,$result);
                    break;
                case 3:
                    $this->calcType3($question,$result);
                    break;
                case 4:
                    $this->calcType4($question,$result);
                    break;
                default:
                    break;
            }
        }

        $this->sumCalculated($homework_id,$userid);
    }

    public function calcType0($question,$result){

        if ($result->answer == $question->getCorrectAnswer()){
            return $result->update(['success_percent' => 100]);
        }else {
            return $result->update(['success_percent' => 0]);
        }
    }

    public function calcType1($question,$result){

        $answers = $question->getAnswer();

        $correct_ans=0;

        foreach ($result as $stRes){

            foreach ( $answers['drop_targets'] as $drops){

                if( $drops->id == $stRes-> option && $drops->correct_option_id == $stRes-> option_matched ||
                    $drops->id ==$stRes-> option_matched  && $drops->correct_option_id == $stRes-> option){
                    $correct_ans++;
                }
            }
        }


        $point = $correct_ans == 0 ? 0 : $correct_ans/count($answers['drop_targets'])*100;

        foreach ($result as $item) {
            if(!$item->update(['success_percent' => $point])){
                abort(500, "Hesaplama sırasında bir hata oluştu!");
            }
        }

    }

    public function calcType3($question,$result){

        $dropCount = QuestionDropTarget::where("question_id",$question->id)->count();
        $answers = $question->getAnswer();
        $totalCorrectCounter = count($answers['target_matchings']);
        $trueAnswerCount = 0;



        foreach ($result as $reslt) {
            foreach ($answers['target_matchings'] as $anstargets) {


                if ( ($reslt->option == $anstargets -> source_drop_id  &&
                    $reslt->option_matched == $anstargets -> destination_drop_id) || $reslt->option == $anstargets -> destination_drop_id  &&
                    $reslt->option_matched == $anstargets -> source_drop_id ){
                    $trueAnswerCount++;
                }
            }
        }

        $givenFalse = count($result) - $trueAnswerCount;
        $totalFalseCount = ( $dropCount * ($dropCount-1) ) - $totalCorrectCounter;
        $tCor = $totalCorrectCounter == 0 ? 0 : ((float)$trueAnswerCount/(float)$totalCorrectCounter);
        $tFal = $totalFalseCount == 0 ? 0 : ((float)$givenFalse/(float)$totalFalseCount);
        $successFloat = ($tCor - $tFal);

        if($successFloat < 0){
            $successFloat = 0;
        }
        $success = (int)round(100 * $successFloat);



        foreach ($result as $item) {
            if(!$item->update(['success_percent' => $success])){
                abort(500, "Hesaplama sırasında bir hata oluştu!");
            }
        }
    }

    public function calcType4($question,$result){

        $answers = $question->getAnswer();

        $givenFalse = 0;
        $trueAnswerCount = 0;
        $totalFalse = 0;
        $totalTrue = 0;

        foreach ($answers['drop_targets'] as $anstargets) {
            if($anstargets->checked == 0){
                $totalFalse++;
            }else{
                $totalTrue++;
            }
        }

        foreach ($result as $reslt) {

            foreach ($answers['drop_targets'] as $anstargets) {

                if ($reslt->option == $anstargets->id  &&  $anstargets->checked == $reslt->option_matched) {
                    $trueAnswerCount++;
                }
                else if ($reslt->option == $anstargets->id  &&  $anstargets->checked != $reslt->option_matched) {
                    $givenFalse++;
                }
            }
        }

        $tCor = $totalTrue == 0.0 ? 0.0 : ((float)$trueAnswerCount/(float)$totalTrue);
        $tFal = $totalFalse == 0.0 ? 0.0 : ((float)$givenFalse/(float)$totalFalse);
        $successFloat = ((($tCor - $tFal) + 1) * 5) * 10;

        if($successFloat < 0){
            $successFloat = 0;
        }



        $success = (int)round($successFloat);


        foreach ($result as $item) {
            if(!$item->update(['success_percent' => $success])){
                abort(500, "Hesaplama sırasında bir hata oluştu!");
            }
        }

    }

    public function sumCalculated($homework_id,$student_id=null){

        $userid = $student_id ? $student_id : Auth::user()->id;
        if(!$userid){
            abort(401);
        }

        $sum =  HomeworkStudentResult::where('homework_id', $homework_id)
            ->where('student_id', $userid)
            ->distinct('question_index')
            ->pluck("success_percent","question_index");

        $tPoint = 0;

        foreach ($sum as $s) {
            $tPoint+= $s;

        }


        $qCount = Question::where("homework_id", $homework_id)
            ->max('question_index');

        $avg = $qCount == 0 ? 0 : $tPoint/$qCount;


        $tTime=0;
        $totalTime = HomeworkStudentResult::where('homework_id', $homework_id)
            ->where('student_id', $userid)
            ->distinct('question_index')
            ->get("time");

        foreach ($totalTime as $s) {
            $tTime+= $s->time;
        }


        HomeworkUser::where("homework_id",$homework_id)
            ->where("user_id", $userid)
            ->update(['point' => round($avg), 'total_time'=> $tTime]);

/*
        $clCamp =  DB::table("users")
            ->select("campus_id","class_id")
            ->LEFTJOIN("class_users", "users.id", "=", "class_users.user_id")
            ->where("users.id", $userid)
            ->first();

        $class_id = $clCamp->class_id;
        $campus_id = $clCamp->campus_id;

        $homework = Homework::find($homework_id);

        $course_id = $homework->homework_course_id;
        $teacher_id = $homework->homework_publisher_id;

        $totalUser = HomeworkUser::where("homework_id",$homework_id)->where("class_id", $class_id)->count();
        $doneUser = HomeworkUser::where("homework_id",$homework_id)->where("class_id", $class_id)->whereNotNull("completed_date")->count();
        $totalPoint = HomeworkUser::where("homework_id",$homework_id)->where("class_id", $class_id)->sum("point");
        $totalTime = HomeworkUser::where("homework_id",$homework_id)->where("class_id", $class_id)->sum("total_time");

        $tPointOBJ = TotalPoint::where("class_id", $class_id)
            ->where("homework_id", $homework_id)
            ->latest()->first();

            $tPointOBJ->homework_id = $homework_id;
            $tPointOBJ->class_id = $class_id;
            $tPointOBJ->campus_id = $campus_id;
            $tPointOBJ->teacher_id = $teacher_id;
            $tPointOBJ->course_id = $course_id;
            $tPointOBJ->total_user = $totalUser;
            $tPointOBJ->done_user = $doneUser;
            $tPointOBJ->total_point = $totalPoint;
            $tPointOBJ->total_time = $totalTime;
            $tPointOBJ->total_point_avg = $totalUser>0 ? $totalPoint/$totalUser : 0;
            $tPointOBJ->save();
*/

    }

}
