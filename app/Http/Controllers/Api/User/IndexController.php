<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class IndexController extends Controller
{
    public function admin_credential_rules(array $data)
    {
        $messages = [
            'current-password.required' => 'Lütfen geçerli şifrenizi giriniz.',
            'password.required' => 'Lütfen şifrenizi giriniz.',
            'password.different' => 'Lütfen farklı bir şifre seçiniz.',
            'password_confirmation.same' => 'Şifre ile tekrarı uyuşmamaktadır.',
            'password_confirmation.required' => 'Lütfen şifrenizi tekrar giriniz.',
        ];

        $validator = Validator::make($data, [
            'current-password' => 'required',
            'password' => 'required|different:current-password',
            'password_confirmation' => 'required|same:password',
        ], $messages);

        return $validator;
    }

    public function changePassword(Request $request)
    {
        if (Auth::Check() ) {
            $request_data = $request->all();
            $validator = $this->admin_credential_rules($request_data);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->getMessageBag()->toArray()
                ], 400);
            } else {
                $current_password = Auth::User()->password;
                if (Hash::check($request_data['current-password'], $current_password)) {
                    $user_id = Auth::User()->id;
                    $obj_user = User::where("id",$user_id)->first();
                    $obj_user->password = Hash::make($request_data['password']);
                    $obj_user->save();
                    return response()->json([
                        'message' => "Şifreniz başarıyla değiştirilmiştir."
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "Lütfen geçerli şifrenizi giriniz."
                    ], 400);
                }
            }
        } else {
            abort(400, "İşlem Reddedildi.");
        }
    }

    public function userInfo(){

        return User::where("id",Auth::user()->id)->with("clsses")->get();

    }

}
