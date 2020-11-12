<?php

namespace App\Http\Controllers\Api\Teacher;

use App\ClassUser;
use App\Clss;
use App\Course;
use App\Homework;
use App\HomeworkStudentResult;
use App\HomeworkUser;
use App\Http\Controllers\Controller;
use App\LeadteacherLesson;
use App\Notification;
use App\NotificationUser;
use App\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Object_;
use Validator;
use App\Http\Controllers\Calculate\CalculateController;


class HomeworkController extends CalculateController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $homework =  new Homework();

        if( Auth::user()->isLeadTeacher() ){

            $courses = LeadteacherLesson::where("teacher_id",Auth::user()->id)->pluck("course_id");
            $homework =  $homework->newQuery()
                ->select("homework.id",  "homework.homework_name","homework.created_at", "users.id as user_id", "users.name",  "courses.id as course_id",   "courses.course_name", "courses.course_level", "lectures.id as lecture_id",  "lectures.lecture_name", "lectures.parent_lecture_id as parentlecture_id", "parentlecture.lecture_name as parentlecture", "homework_users.deadline")
                ->selectRaw('ifnull((count(homework_users.completed_date) / count(homework_users.point) *100),0) as completed ')
                ->selectRaw('ifnull((ifNull( AVG(homework_users.point),0) * 100/ifnull((count(homework_users.completed_date) / count(homework_users.point) *100),1) ),0) as avgpoint')
                ->whereIn('homework_course_id', $courses)
                ->LEFTJOIN ("users", "users.id", "=", "homework.homework_publisher_id")
                ->LEFTJOIN ("courses", "courses.id", "=", "homework.homework_course_id")
                ->LEFTJOIN ("lectures", "lectures.id", "=", "homework.homework_lecture_id")
                ->LEFTJOIN ("lectures as parentlecture", "parentlecture.id", "=", "lectures.parent_lecture_id")
                ->LEFTJOIN ("homework_users", "homework_users.homework_id", "=", "homework.id")
                ->groupBy("id");

        }else if( Auth::user()->hasRole("teacher") ){

            $homework =  $homework->newQuery()
                ->select("homework.id", "homework.homework_name","homework.created_at", "users.id as user_id", "users.name",  "courses.id as course_id",   "courses.course_name", "courses.course_level", "lectures.id as lecture_id",  "lectures.lecture_name", "lectures.parent_lecture_id as parentlecture_id", "parentlecture.lecture_name as parentlecture", "homework_users.deadline")
                ->selectRaw('ifnull((count(homework_users.completed_date) / count(homework_users.point) *100),0) as completed ')
                ->selectRaw('ifnull((ifNull( AVG(homework_users.point),0) * 100/ifnull((count(homework_users.completed_date) / count(homework_users.point) *100),1) ),0) as avgpoint')
                ->where("homework_publisher_id", Auth::user()->id)
                ->LEFTJOIN ("users", "users.id", "=", "homework.homework_publisher_id")
                ->LEFTJOIN ("courses", "courses.id", "=", "homework.homework_course_id")
                ->LEFTJOIN ("lectures", "lectures.id", "=", "homework.homework_lecture_id")
                ->LEFTJOIN ("lectures as parentlecture", "parentlecture.id", "=", "lectures.parent_lecture_id")
                ->LEFTJOIN ("homework_users", "homework_users.homework_id", "=", "homework.id")
                ->groupBy("id");
        }
        else if( Auth::user()->hasRole("admin") ){

            $homework =  $homework->newQuery()
                ->select("homework.id", "homework.homework_name","homework.created_at", "users.id as user_id", "users.name",  "courses.id as course_id",   "courses.course_name", "courses.course_level", "lectures.id as lecture_id",  "lectures.lecture_name", "lectures.parent_lecture_id as parentlecture_id", "parentlecture.lecture_name as parentlecture", "homework_users.deadline")
                ->selectRaw('ifnull((count(homework_users.completed_date) / count(homework_users.point) *100),0) as completed ')
                ->selectRaw('ifnull((ifNull( AVG(homework_users.point),0) * 100/ifnull((count(homework_users.completed_date) / count(homework_users.point) *100),1) ),0) as avgpoint')
                ->LEFTJOIN ("users", "users.id", "=", "homework.homework_publisher_id")
                ->LEFTJOIN ("courses", "courses.id", "=", "homework.homework_course_id")
                ->LEFTJOIN ("lectures", "lectures.id", "=", "homework.homework_lecture_id")
                ->LEFTJOIN ("lectures as parentlecture", "parentlecture.id", "=", "lectures.parent_lecture_id")
                ->LEFTJOIN ("homework_users", "homework_users.homework_id", "=", "homework.id")
                ->groupBy("id");

        } else {
            abort(401,"Yetkisiz Erişim!");
        }

        $req = $request->all();
        foreach ($req as $key => $item) {
            $req[$key] = str_replace("ı", "i" , $req[$key]);
        }

        if ($request->has('homework_name') && $req['homework_name']) {
            $homework->where('homework_name', 'LIKE' ,  '%'.$req['homework_name'].'%');
        }

        if ($request->has('name') && $req['name']) {
            $homework->where('users.name', 'LIKE' ,  '%'.$req['name'].'%');
        }

        if ($request->has('course_level') && $req['course_level']) {
            $homework->where('courses.course_level', 'LIKE' ,  '%'.$req['course_level'].'%');
        }

       if ($request->has('lecture_name') && $req['lecture_name']) {
            $homework->where('lectures.lecture_name', 'LIKE' ,  '%'.$req['lecture_name'].'%')->where("lectures.parent_lecture_id", ">", 0);
        }

        if ($request->has('parentlecture') && $req['parentlecture']) {
            $homework->where(function($homework) use($req) {
                $homework->where('parentlecture.lecture_name', 'LIKE' ,  '%'.$req['parentlecture'].'%')->where("parentlecture.parent_lecture_id", "=", "0")
                    ->orWhere('lectures.lecture_name', 'LIKE' ,  '%'.$req['parentlecture'].'%')->where("lectures.parent_lecture_id", "=", "0");
            });
        }

        if ($request->has('sortAsc') && $request->input('sortAsc')) {
            $homework->orderBy( $request->input('sortAsc'), 'ASC');
        }

        if ($request->has('sortDesc') && $request->input('sortDesc')) {
            $homework->orderBy( $request->input('sortDesc'), 'DESC');
        }

        return $homework->paginate(20);
    }

    protected function sortData(Collection $collection){

        if(request()->has('sortAsc')){
            $attribute = request()->sortAsc;
            $collection = $collection->sortBy($attribute);
        }else if(request()->has('sortDesc')){
            $attribute = request()->sortDesc;
            $collection = $collection->sortByDesc($attribute);
        }

        return $collection;

    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    protected function filterData(Collection $collection, $transformer){

        foreach ( request()->query() as $query => $value ) {

            $attribute = $transformer::originalAttribure($query);

            if(isset($attribute, $value)){
                $collection = $collection->where($attribute, "LIKE", "%".$value."%");
            }

            return $collection;

        }

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
     * @param  \App\Homework  $homework
     * @return \Illuminate\Http\Response
     */
    public function show(Homework $homework)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Homework  $homework
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Homework $homework)
    {

        $canDelete = Auth::user()->id === $homework->homework_publisher_id;
        if(!$canDelete){
            abort(400,"Sadece Kendi Ödevlerinizi Silebilirsiniz.");
        }

        $req =  $request->all("deadline");

        $validationRules = [
            'deadline' => 'required|date_format:Y-m-d H:i:s|after:1 seconds'
        ];
        $validator = Validator::make($req,$validationRules);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        $valid =  HomeworkUser::where("homework_id", $homework->id)
            ->update($req);

        if(!$valid){
            return response()->json([
                'message' => "Ödev tarihi güncelleme sırasında bir hata oluştu"
            ], 500);
        }

        return response()->json([
            'message' => "Ödev bitiş tarihi başarıyla güncellendi"
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Homework  $homework
     * @return \Illuminate\Http\Response
     */
    public function destroy(Homework $homework)
    {
        $canDelete = Auth::user()->id === $homework->homework_publisher_id;
        if(!$canDelete){
            abort(400,"Sadece Kendi Ödevlerinizi Silebilirsiniz.");
        }
        HomeworkUser::where("homework_id",$homework->id)->delete();
        NotificationUser::where("homework_id",$homework->id)->delete();
        $homework->delete();

        return response()->json([
            'message' => 'Ödev Başarıyla Silindi'
        ]);


    }

    public function comment(Request $request, HomeworkUser $homeworkuser)
    {

        $teacher_comment =  $request->all()['teacher_comment'];

        if(!$teacher_comment){
            return response()->json([
                'message' => "Öğretmen Yorumu Gereklidir!"
            ], 400);
        }

        $homeworkuser->update(['teacher_comment' => $teacher_comment]);
        $notificationObj = Notification::find(2);
        $notificationObj->users()->attach(['user_id'=> $homeworkuser->user_id]);
        $hm = NotificationUser::latest()->first();
        $hm->homework_id = $homeworkuser->homework_id;
        $hm->save();

        return $request->all();
    }


    public function getClasses(Homework $homework){

        $allClasses =  HomeworkUser::select("class_id")
            ->where("homework_id",$homework->id)
            ->groupBy("class_id")
            ->pluck("class_id");

            $students = HomeworkUser::where("homework_id", $homework->id)->get();
            $clss = [];
            foreach ($students as $student) {
                $clsss = ClassUser::where("user_id", $student->user_id)->pluck("class_id");
                foreach ($clsss as $c) {
                    $clss[] = $c;
                }
            }
            $clss = array_unique($clss);

            $uclass = [];
            $ii = array_unique($clss);
            foreach ($ii as $i) {
                $cl = Clss::find($i);
                $uclass[] = Clss::find($i);
            }


        $returnClass = [];
            foreach ($uclass as $uclas) {
                $students = ClassUser::where("class_id", $uclas->id)->pluck("id")->toArray();
                $done = HomeworkUser::where("homework_id", $homework->id)
                    ->whereIn("user_id", $students)
                    ->whereNotNull("completed_date")
                    ->count();
                $all = HomeworkUser::where("homework_id", $homework->id)
                    ->whereIn("user_id", $students)
                    ->count();
                $uclas->done_percent = $done == 0 ? 0 : $done/$all*100;

                $avg = HomeworkUser::where("homework_id", $homework->id)
                    ->whereIn("user_id", $students)
                    ->whereNotNull("completed_date")
                    ->avg("point");

                $uclas->point_avg = $avg ? $avg : 0;
                $returnClass[] = $uclas;
            }

            return $returnClass;
        }

        public function getQuestionResults(Homework $homework, User $user, $withImage=0){

            $HomeworkStudentResults =  HomeworkStudentResult::where("student_id", $user->id)
                ->where("homework_id", $homework->id)
                ->get();
            if(!count($HomeworkStudentResults)){
                return response()->json([
                    'message' => "Ödev Henüz Cevaplanmamış."
                ], 204);
            }
            $HomeworkStudentResults->makeVisible('success_percent')->makeHidden('current_matching');

            if(!$withImage){
                $HomeworkStudentResults->makeHidden('screenshot');
                $HomeworkStudentResults->makeHidden('audio');
            }

            return response()->json(
                $HomeworkStudentResults,200);
        }


        public function rateQuestion(HomeworkStudentResult $homeworkStudentResult, Request $request){


            if($homeworkStudentResult->type != 5){
               abort(400, "Sadece açık uçlu soru puanlanabilir");
            }

            $canRate = Auth::user()->id === $homeworkStudentResult->getTeacherID();
            if(!$canRate){
                abort(400,"Sadece Kendi Ödevlerinizi Puanlayabilirsiniz.");
            }

            $validationRules = [
                'teacher_rate' => 'required',

            ];
            $validator = Validator::make($request->all(),$validationRules);

            if($validator ->fails()){
                return response()->json([
                    'message' => $validator->messages()
                ],400);
            }

            $homeworkStudentResult->success_percent = $request->all()['teacher_rate'];
            $homeworkStudentResult->save();

            $this->sumCalculated($homeworkStudentResult->homework_id,$homeworkStudentResult->student_id);

            return $request->all();
        }

        public function homeworkClassTable(){


            if( Auth::user()->isLeadTeacher() ){

                $courses = LeadteacherLesson::where("teacher_id",Auth::user()->id)->pluck("course_id");
                $classes = [];
                $clssa = array_values(HomeworkUser::LEFTJOIN("homework", "homework.id", "=", "homework_users.homework_id")
                    ->whereIN("homework.homework_course_id", $courses)
                    ->orWhere("homework.homework_publisher_id", Auth::user()->id)
                    ->get("class_id")->unique("class_id")->toArray());

                foreach ($clssa as $val) {
                    $classes[] = $val['class_id'];
                }
            }
            elseif( Auth::user()->hasRole("teacher") ) {
                $classes = [];
                $clssa = array_values(HomeworkUser::LEFTJOIN("homework", "homework.id", "=", "homework_users.homework_id")
                    ->where("homework.homework_publisher_id", Auth::user()->id)
                    ->get("class_id")->unique("class_id")->toArray());
                foreach ($clssa as $val) {
                    $classes[] = $val['class_id'];
                }
            }

            if( Auth::user()->hasRole("manager") ){
                $campusTeachers = User::where("role",2)
                    ->where("campus_id", Auth::user()->campus_id)
                    ->pluck("id");
                $classes = [];
                $clssa = array_values(HomeworkUser::LEFTJOIN("homework", "homework.id", "=", "homework_users.homework_id")
                    ->whereIn("homework.homework_publisher_id", $campusTeachers)
                    ->orWhere("homework.homework_publisher_id", Auth::user()->id)
                    ->get("class_id")->unique("class_id")->toArray());
                foreach ($clssa as $val) {
                    $classes[] = $val['class_id'];
                }
            }

            if( Auth::user()->hasRole("admin") ){

                return DB::select( DB::raw("select  cls.id, cls.class_name,
                        IFNULL( (count(NULLIF(hus.completed_date,'') ) /count(hus.id) *100),0)  as done_percent,
                        IFNULL(sum(point)/count(NULLIF(hus.completed_date,'')), 0) as avg

                        from homework_users as hus,clsses as cls,homework as hw
                        WHERE cls.id=hus.class_id
                        AND hw.id=hus.homework_id
                        AND hw.deleted_at is null
                        group by class_id
                        order by class_name") );
/*
                if (Cache::has('class2Admin'))
                {
                    $returnClss = Cache::get('class2Admin');

                }else {
                    $campusTeachers = User::where("role",2)
                        ->pluck("id");
                    $classes = [];
                    $clssa = array_values(HomeworkUser::LEFTJOIN("homework", "homework.id", "=", "homework_users.homework_id")
                        ->whereIn("homework.homework_publisher_id", $campusTeachers)
                        ->orWhere("homework.homework_publisher_id", Auth::user()->id)
                        ->get("class_id")->unique("class_id")->toArray());

                    foreach ($clssa as $val) {
                        $classes[] = $val['class_id'];
                    }

                    $returnClss = [];
                    foreach ($classes as $class_id) {
                        $clObj = Clss::find($class_id);

                        $cLs = $this->classHomeworkTable($clObj);
                        $cnt = 0;
                        $ttl = 0;
                        $avg = 0;
                        foreach ($cLs as $item) {
                            $ttl += $item->done_percent;
                            $cnt = $item->done_percent > 0 ? $cnt + 1 : $cnt;
                            $avg += $item->avg;
                        }
                        $clObj['avg'] = $cnt > 0 && $avg > 0 ? $avg / $cnt : 0;
                        $clObj['done_percent'] = count($cLs) > 0 && $ttl > 0 ? $ttl / count($cLs) : 0;
                        $returnClss[] = $clObj;
                    }

                    Cache::put('class2Admin', $returnClss, 600);

                }
                return $returnClss;
*/
            }
            $returnClss = [];
            foreach ($classes as $class_id) {
                $clObj = Clss::find($class_id);

                $cLs = $this->classHomeworkTable($clObj);
                $cnt = 0;
                $ttl = 0;
                $avg = 0;
                foreach ($cLs as $item) {
                    $ttl += $item->done_percent;
                    $cnt = $item->done_percent > 0 ? $cnt + 1 : $cnt;
                    $avg += $item->avg;
                }
                $clObj['avg'] = $cnt > 0 && $avg > 0 ? $avg / $cnt : 0;
                $clObj['done_percent'] = count($cLs) > 0 && $ttl > 0 ? $ttl / count($cLs) : 0;
                $returnClss[] = $clObj;
            }
            return $returnClss;

        }

        public function classHomeworkTable(Clss $clss){


            if( Auth::user()->isLeadTeacher() ){

                $courses = LeadteacherLesson::where("teacher_id",Auth::user()->id)->pluck("course_id");
                $hUser =  HomeworkUser::select("homework_id")
                    ->LEFTJOIN("homework", "homework.id", "=" , "homework_users.homework_id")
                    ->whereIN("homework.homework_course_id", $courses)
                    ->orWhere("homework.homework_publisher_id", Auth::user()->id)
                    ->where("class_id", $clss->id)
                    ->groupBy("homework_id")
                    ->pluck("homework_id");

            }elseif( Auth::user()->hasRole("teacher") ) {

                $hUser = HomeworkUser::select("homework_id")
                    ->LEFTJOIN("homework", "homework.id", "=", "homework_users.homework_id")
                    ->where("homework.homework_publisher_id", Auth::user()->id)
                    ->where("class_id", $clss->id)
                    ->groupBy("homework_id")
                    ->pluck("homework_id");
            }

            if( Auth::user()->hasRole("manager") ){
                $campusTeachers = User::where("role",2)
                    ->where("campus_id", Auth::user()->campus_id)
                    ->pluck("id");

                $hUser = HomeworkUser::select("homework_id")
                    ->LEFTJOIN("homework", "homework.id", "=", "homework_users.homework_id")
                    ->whereIN("homework.homework_publisher_id", $campusTeachers)
                    ->orWhere("homework.homework_publisher_id", Auth::user()->id)
                    ->where("class_id", $clss->id)
                    ->groupBy("homework_id")
                    ->pluck("homework_id");

            }

            if( Auth::user()->hasRole("admin") ){
                $allTeachers = User::where("role",2)
                    ->pluck("id");

                $hUser = HomeworkUser::select("homework_id")
                    ->LEFTJOIN("homework", "homework.id", "=", "homework_users.homework_id")
                    ->whereIN("homework.homework_publisher_id", $allTeachers)
                    ->orWhere("homework.homework_publisher_id", Auth::user()->id)
                    ->where("class_id", $clss->id)
                    ->groupBy("homework_id")
                    ->pluck("homework_id");
            }

            $homeworks = [];
            foreach ($hUser as $item) {

                $tmp = Homework::where("id",$item)->with("teacher","course","lecture","lecture.parentlecture")->first();

                $tmp -> deadline = $tmp->getdeadline->first();
                unset($tmp->getdeadline);
                $all = HomeworkUser::where("class_id", $clss->id)->where("homework_id", $item)->count();
                $done = HomeworkUser::where("class_id", $clss->id)->where("homework_id", $item)->whereNotNull("completed_date")->count();
                $avvg = HomeworkUser::where("class_id", $clss->id)->where("homework_id", $item)->sum("point");

                $tmp ->avg = $avvg && $done ? $avvg / $done : 0;
                $tmp ->done_percent =  $done ? $done/$all*100 : 0;
                $homeworks[] = $tmp;
            }

            return $homeworks;
        }


        public function managerGetLessons(){
            //return Cache::remember('managerClass', config('teaoptions.cacheTime'), function () {
                return  DB::table('homework')
                    ->select('courses.course_name','courses.course_level','homework_course_id', DB::raw('count(*) as total'))
                    ->leftJoin('courses', 'courses.id', '=', 'homework.homework_course_id')
                    ->whereNull('homework.deleted_at')
                    ->groupBy('homework.homework_course_id')
                    ->get();
           // });
        }


        public function managerGetTeachers($course_id){
           // return Cache::remember('managerClassTeacher', config('teaoptions.cacheTime'), function () use ($course_id) {
                return  DB::table('homework')
                    ->select( 'users.name', 'homework_publisher_id', DB::raw('count(*) as total'))
                    ->leftJoin('users', 'users.id', '=', 'homework.homework_publisher_id')
                    ->where('homework_course_id', $course_id)
                    ->whereNull('homework.deleted_at')
                    ->groupBy('homework_publisher_id')
                    ->get();
            //});

        }


        public function managerGetHomeworks($course_id,$publisher_id){

            $homework =  Homework::where('homework_course_id', $course_id)
                ->where("homework_publisher_id", $publisher_id)
                ->with("teacher","course","lecture")
                ->whereNull('homework.deleted_at')
                ->orderBy('id', 'DESC')
                ->get();


            $homework->each(function ($i, $k){
                $i->deadline = $i->getdeadline->first();
                $i->done_percent = $i->getDonePercent();
                $i->point_avg = $i->getPointAvg();
                unset($i->getdeadline);
            });

            return $homework;
        }


    public function managerGetClasses(){
       // return Cache::remember('managerClassTable2', config('teaoptions.cacheTime'), function () {
            return DB::select( DB::raw("select class_id, class_name, count(class_id) as count from (
                select clsses.id as class_id, clsses.class_name as class_name from homework_users
                left join clsses on clsses.id=homework_users.class_id
                GROUP BY homework_users.class_id,homework_users.homework_id
                ORDER BY clsses.id,homework_users.homework_id ) as vTable
                GROUP BY class_id;") );
       // });
    }
    public function managerGetClasses2($class_id){
     //   return Cache::remember('managerClassTable2', config('teaoptions.cacheTime'), function () use ($class_id) {
            return DB::select( DB::raw("select  homework_course_id,course_name,count(homework_course_id) as count from (
                select homework_id, homework_course_id, count(homework_course_id) as count, course_name
                from homework_users as hus, homework as hw, courses
                where hw.id = hus.homework_id
                AND hw.deleted_at is null
                AND hus.class_id=$class_id
                AND courses.id = hw.homework_course_id
                group by homework_id) as vTable
                group by course_name;") );
       // });
    }

    public function managerGetClasses3($class_id,$course_id){
      //  return Cache::remember('managerClassTable2', config('teaoptions.cacheTime'), function () use ($class_id,$course_id) {
            return DB::select( DB::raw("select homework_id,homework_name,count(homework_id) as count,
            count(NULLIF(hus.completed_date,'')) as completed, IFNULL(sum(point)/count(NULLIF(hus.completed_date,'')), 0) as avg
            from homework_users as hus ,homework as hw
            where hw.id = hus.homework_id
            AND hw.deleted_at is null
            AND hus.class_id=$class_id AND hw.homework_course_id=$course_id
            group by homework_id") );
      //  });
    }


}
