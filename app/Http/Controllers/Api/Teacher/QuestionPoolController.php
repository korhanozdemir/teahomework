<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Information;
use App\QuestionDropTarget;
use App\QuestionImage;
use App\QuestionOption;
use App\QuestionPool;
use App\TargetMatching;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class QuestionPoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return QuestionPool::with('user','lecture','sublecture','course')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = [
            'question_type' => 'required|numeric',
            'lecture' => 'required|numeric',
            'sublecture' => 'required|numeric',
            'level' => 'required|numeric',
            'course' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(),$validationRules);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }


        $question = new QuestionPool();
        $question -> question_publisher_id = Auth::user()->id;
        $question -> question_type = $request->question_type;
        $question -> option_count = $request->option_count;
        $question -> answer = $request->answer;
        $question -> correct_answer = $request->correct_answer;
        $question -> rating = $request->rating;
        $question -> ratable = $request->ratable;
        $question -> level = $request->level;
        $question -> course = $request->course;
        $question -> lecture = $request->lecture;
        $question -> sublecture = $request->sublecture;
        $question -> difficulties = $request->difficulties;
        $question -> desc = $request->desc;
        $question->save();

        foreach ($request->images as $qimg){
            $image = new QuestionImage();
            $image -> image_url = $qimg['image_url'];
            $image -> bounds = $qimg['bounds'];
            $question->images()->save($image);
        }

        $qoptid= [];
        foreach ($request->options as $qopt){
            $qoptObj = new QuestionOption();
            $qoptObj -> option_text = $qopt['option_text'];
            $question->options()->save($qoptObj);
            $qoptid[] = $qoptObj->id;
        }

        foreach ($request->drop_targets as $qdrop){
            $qdropObj = new QuestionDropTarget();
            $qdropObj -> bounds = $qdrop['bounds'];
            $qdropObj -> anchor = isset($qdrop['anchor']) ? $qdrop['anchor'] : null;
            $qdropObj -> checked = $qdrop['checked'];
            $qdropObj -> correct_option_id = isset($qdrop['correct_option_id']) ? $qoptid[$qdrop['correct_option_id']] : null;
            $question->dropTargets()->save($qdropObj);
            if($request->question_type == 3){
                $qoptid[] = $qdropObj->id;
            }
        }


        foreach ($request->information as $qinf){
            $qinfObj = new Information();
            $qinfObj -> inf_type = $qinf['inf_type'];
            $qinfObj -> media_type = $qinf['media_type'];
            $qinfObj -> inf_url = $qinf['inf_url'];
            $qinfObj -> bounds = $qinf['bounds'];
            $qinfObj -> visible_before = $qinf['visible_before'];
            $qinfObj -> text = $qinf['text'];
            $question->information()->save($qinfObj);
        }

        foreach ($request->target_matching as $qtarget){
            $qtargetObj = new TargetMatching();
            $qtargetObj -> source_option_id = isset($qtarget['source_option_id']) ? $qoptid[$qtarget['source_option_id']] : null;
            $qtargetObj -> destination_option_id = isset($qtarget['destination_option_id']) ? $qoptid[$qtarget['destination_option_id']] : null;


            $qtargetObj -> source_drop_id = isset($qtarget['source_drop_id']) ? $qoptid[$qtarget['source_drop_id']] : null;
            $qtargetObj -> destination_drop_id = isset($qtarget['destination_drop_id']) ? $qoptid[$qtarget['destination_drop_id']] : null;
            $question -> targetMatching()->save($qtargetObj);
        }

        $quest =  QuestionPool::with('images', 'options', 'dropTargets','information', 'targetMatching')
            ->find($question->id);

        return response([
            'data' => $quest,
            'message' => 'Soru Eklendi'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\QuestionPool  $questionPool
     * @return \Illuminate\Http\Response
     */
    public function show(QuestionPool $questionPool)
    {
        return QuestionPool::with('user','lecture','sublecture','course','images', 'options', 'dropTargets','information', 'targetMatching')
            ->find($questionPool->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\QuestionPool  $questionPool
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuestionPool $questionPool)
    {

        $validationRules = [
            'question_type' => 'required|numeric',
            'lecture' => 'required|numeric',
            'sublecture' => 'required|numeric',
            'level' => 'required|numeric',
            'course' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(),$validationRules);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }
        if($questionPool->question_publisher_id !==Auth::user()->id){
            abort(400,"Sadece kendi sorunuzu düzenleyebilrisiniz.");
        }


        $question = new QuestionPool();
        $question -> question_publisher_id = Auth::user()->id;
        $question -> question_type = $request->question_type;
        $question -> option_count = $request->option_count;
        $question -> answer = $request->answer;
        $question -> correct_answer = $request->correct_answer;
        $question -> rating = $request->rating;
        $question -> ratable = $request->ratable;
        $question -> level = $request->level;
        $question -> course = $request->course;
        $question -> lecture = $request->lecture;
        $question -> sublecture = $request->sublecture;
        $question -> difficulties = $request->difficulties;
        $question -> desc = $request->desc;
        $question->save();

        foreach ($request->images as $qimg){
            $image = new QuestionImage();
            $image -> image_url = $qimg['image_url'];
            $image -> bounds = $qimg['bounds'];
            $question->images()->save($image);
        }

        $qoptid= [];
        foreach ($request->options as $qopt){
            $qoptObj = new QuestionOption();
            $qoptObj -> option_text = $qopt['option_text'];
            $question->options()->save($qoptObj);
            $qoptid[] = $qoptObj->id;
        }

        foreach ($request->drop_targets as $qdrop){
            $qdropObj = new QuestionDropTarget();
            $qdropObj -> bounds = $qdrop['bounds'];
            $qdropObj -> anchor = $qdrop['anchor'];
            $qdropObj -> checked = $qdrop['checked'];
            $qdropObj -> correct_option_id = isset($qdrop['correct_option_id']) ? $qoptid[$qdrop['correct_option_id']] : null;
            $question->dropTargets()->save($qdropObj);
            if($request->question_type == 3){
                $qoptid[] = $qdropObj->id;
            }
        }


        foreach ($request->information as $qinf){
            $qinfObj = new Information();
            $qinfObj -> inf_type = $qinf['inf_type'];
            $qinfObj -> media_type = $qinf['media_type'];
            $qinfObj -> inf_url = $qinf['inf_url'];
            $qinfObj -> bounds = $qinf['bounds'];
            $qinfObj -> visible_before = $qinf['visible_before'];
            $qinfObj -> text = $qinf['text'];
            $question->information()->save($qinfObj);
        }

        foreach ($request->target_matching as $qtarget){
            $qtargetObj = new TargetMatching();
            $qtargetObj -> source_option_id = isset($qtarget['source_option_id']) ? $qoptid[$qtarget['source_option_id']] : null;
            $qtargetObj -> destination_option_id = isset($qtarget['destination_option_id']) ? $qoptid[$qtarget['destination_option_id']] : null;


            $qtargetObj -> source_drop_id = isset($qtarget['source_drop_id']) ? $qoptid[$qtarget['source_drop_id']] : null;
            $qtargetObj -> destination_drop_id = isset($qtarget['destination_drop_id']) ? $qoptid[$qtarget['destination_drop_id']] : null;
            $question -> targetMatching()->save($qtargetObj);
        }

        $questionPool->delete();

        $quest =  QuestionPool::with('user','lecture','sublecture','course','images', 'options', 'dropTargets','information', 'targetMatching')
            ->find($question->id);

        return response([
            'data' => $quest,
            'message' => 'Soru Güncellendi'
        ]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QuestionPool  $questionPool
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuestionPool $questionPool)
    {

        if($questionPool->question_publisher_id !==Auth::user()->id){
            abort(400,"Sadece kendi sorunuzu silebilirsiniz.");
        }

        $deleted = $questionPool->delete();

        if(!$deleted){
            abort(400,"Bir hata oluştu");
        }

        return response([
            'message' => 'Soru Güncellendi'
        ]);
    }

    public function duplicate($id)
    {
        $question = QuestionPool::find($id);
        $newQuestion = $question->replicate();
        $newQuestion -> question_publisher_id = Auth::user()->id;
        $newQuestion->save();


        return response( $newQuestion,200 );
    }
}
