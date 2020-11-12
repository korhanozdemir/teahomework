<?php

namespace App\Http\Controllers\Api\Student;

use App\HomeworkUser;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Mail\SendEmail;
use App\NotificationUser;
use App\User;
use App\Campus;
use App\Clss;
use App\Lecture;
use App\Course;
use App\Homework;
use App\Question;
use App\HomeworkStudentResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
                ->get()
                ->makeHidden('screenshot')
                ->makeHidden('audio');

            $homework['student_answers'] = $answers;

            return response()->json($homework, 200);


        }else {
            abort(403);
        }
    }

    public function getAllHomework(){

        $homework = HomeworkUser::where('user_id', Auth::user()->id)
            ->where('start_date', '<=', Carbon::now())
            ->with("homework")->get();


        $preSuccessDone = [];
        $preLateDone = [];
        $preWaiting = [];
        $futureDone = [];
        $futureWaiting = [];

        foreach ($homework as $h){
            if($h->deadline < Carbon::now()){
                if($h->completed_date == null ){
                    $preWaiting[] = $h;
                }else if($h->completed_date <=  $h ->deadline ){
                    $preSuccessDone[] = $h;
                }else if($h->completed_date >  $h ->deadline ){
                    $preLateDone[] = $h;
                }
            }else {
                if($h->completed_date == null){
                    $futureWaiting[]=$h;
                }else {
                    $futureDone[] = $h;
                }
            }
        }

        return response([
            'previous' => [
                'previousSuccess' => $preSuccessDone,
                'previousLateDone' => $preLateDone,
                'previousWaiting' => $preWaiting,
            ],
            'future' => [
                'futureDone' => $futureDone,
                'futureWaiting' => $futureWaiting
            ],
        ]);


    }

    public function setHomeworkTime($id){
        $HomeworkUser =  HomeworkUser::where("homework_id", $id)
            ->where("user_id",Auth::user()->id)
            ->whereNull("started_at")->update(['started_at'=> Carbon::now()]);

        return HomeworkUser::select(DB::raw("GREATEST(((homework.homework_time - TIME_TO_SEC(TIMEDIFF(NOW(), started_at)))),0) as expires_in"))
            ->where("homework_id", $id)
            ->where("user_id",Auth::user()->id)
            ->leftJoin("homework", "homework.id", "=", "homework_users.homework_id")
            ->first("expires_in");
    }

    public function getAllNotifications(){

        $notif = DB::select(DB::raw("select notf.id as ntype,hw.homework_name, notf.text,co.course_name, nus.homework_id, nus.is_read,nus.created_at
            from notification_users as nus, notifications as notf, homework as hw,courses as co
            where notf.id=nus.text_id
            AND hw.id=nus.homework_id
            AND hw.homework_course_id=co.id
            AND nus.user_id=".Auth::user()->id));

        return response([
            'notifications' => $notif
        ],200);
    }

    public function setAllNotificationsAsRead(){
        $Notif = NotificationUser::where("user_id",Auth::user()->id)->update(['is_read' => 1]);
        return response([

        ],200);

    }

    public function test(){
        return Auth::user()->getAutoLoginKey();
    }



}
