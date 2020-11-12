<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SsoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login($usercode,$md5pass,$date)
    {

        if($date != md5(date("d-m-Y:H")) && $date!= md5(date("d-m-Y:H",strtotime(' -1 hour') ))){
            abort(400, "Invalid Parameters");
        }

        if(!$usercode || !$md5pass){
            abort(400, "Usercode and password is reqired!");
        }

        $user = User::where("code", $usercode)->first();

        if(!$user){
            Session::put('login_error', 'Your usercode and password wrong!!');
            return back();
        }

        if( md5($user->getAutoLoginKey()) == $md5pass ){
            $passwsso = $user->getAutoLoginKey();
        }else {
            Session::put('login_error', 'Your usercode and password wrong!!');
            return back();
        }

        if (auth()->guard('web')->attempt(['code' => $usercode, 'password' => $passwsso])) {

            $new_session_id = Session::getId(); //get new session_id after user sign in

            if ($user->session_id != '') {
                $last_session = Session::getHandler()->read($user->session_id);

                if ($last_session) {
                    if (Session::getHandler()->destroy($user->session_id)) {

                    }
                }
            }

            User::where('id', $user->id)->update(['session_id' => $new_session_id]);

            $user = auth()->guard('web')->user();

            return redirect($this->redirectTo);
        }
        Session::put('login_error', 'Your email and password wrong!!');
        return back();

    }

}
