<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','api_token','password', 'code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','api_token','deleted_at', 'created_at','updated_at'
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function find($user_id)
    {

    }

    public function hasRole($role){
        $hasRole = Roles::Select("users.id")
            ->leftJoin("users", "users.role", "=", "roles.id")
            ->where("roles.name", $role)
            ->where ("users.id", Auth::user()->id)
            ->count();
        if($hasRole>0){
            return true;
        }else {
            return false;
        }
    }

    public function getRole(){
        $role = Roles::select("name")
        ->find($this->role);

        if(isset($role['name'])){
            return $role['name'];
        }else {
            return false;
        }
    }

    public function getStudentUserDetails(){
        $authuser = Auth::user();
        if( !isset($authuser->role) || $authuser->role !== 3){
            return 0;
        }

        $student = $authuser->getStudentClass();
        $student = $student ? $student->toArray() : [];
        return (array_merge($authuser->toArray(), $student));


    }

    public function getStudentClass(){
        $userid = Auth::user()->id;
        $classId = ClassUser::select("class_users.class_id","clsses.class_name","clsses.class_level")
            ->where("user_id",$userid)
            ->leftJoin("clsses", "clsses.id", "=", "class_users.class_id")
            ->where("clsses.is_class_mixed", 0)
            ->first();
        return $classId;
    }

    public function getActiveHomework(){

        $user_id = Auth::user()->id;
        $homeworks = HomeworkUser::where("user_id", $user_id)
            ->where("start_date","<",date('Y-m-d H:i:s'))
            ->get();
        return $homeworks;
    }

    public function clsses(){
        return $this->belongsToMany('App\Clss', 'class_users', 'user_id', 'class_id');
    }

    public function homeworks(){
        return $this->belongsToMany('App\Homework', 'homework_users', 'user_id', 'homework_id');
    }

    public function roleName(){
        return $this->belongsTo('App\Roles','id');
    }

    public function notifications(){
        return $this->belongsToMany('App\Notification', 'notification_users', 'user_id', 'text_id')
            ->orderBy('notification_users.created_at','DESC')
            ->withPivot('is_read','homework_id')
            ->withTimestamps();
    }

    public function getPassword(){
        return $this->password;
    }

    public function getClass(){
        return ClassUser::where("user_id",$this->id)->pluck("class_id")[0];
    }

    public function getAutoLoginKey(){
        return $this->clsses->first()->class_name.preg_replace('/[^0-9]/', '', $this->code );
    }

    public function isLeadTeacher(){
        $isLead = LeadteacherLesson::where("teacher_id", $this->id)->count() ? 1 : 0;
        return $isLead;
    }


}
