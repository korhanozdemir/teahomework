<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Clss;
use App\Homework;
use App\HomeworkUser;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class HomeworkUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HomeworkUser  $homeworkUser
     * @return \Illuminate\Http\Response
     */
    public function show(HomeworkUser $homeworkUser)
    {
        return HomeworkUser::find($homeworkUser->id)->with("user")->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HomeworkUser  $homeworkUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HomeworkUser $homeworkUser)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HomeworkUser  $homeworkUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(HomeworkUser $homeworkUser)
    {
        abort(404);
    }

    public function classStudent(Homework $homework, Clss $clss){

        $students = $clss->students;
        $st = [];

        foreach ($students as $student) {
            $hmwrk = HomeworkUser::where("user_id", $student->id)
                ->where("homework_id", $homework->id);
            if ($hmwrk ->count() > 0 ){
                $student['homework_user'] = $hmwrk->first();
                $st[] = $student;
            }
        }
        return $st;
    }






}
