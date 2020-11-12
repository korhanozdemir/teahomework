<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
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

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'code';

        $user = User::where($fieldType, $request->input('email'))->first();

        if (auth()->guard('web')->attempt([$fieldType => $request->input('email'), 'password' => $request->input('password')])) {

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

    public function logout(Request $request)
    {
        Session::flush();
        Session::put('success', 'Logout Successful!');
        return redirect()->to('/login');
    }
}
