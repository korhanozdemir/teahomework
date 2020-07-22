<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\User;
use Str;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){

        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required'
        ]);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ]);
        }
        $user = User::where('role', 2)
            ->where('email', $request->input('username'))
            ->first();
        if($user){
            if(Hash::check($request->input('password'), $user->password)){
                $token = Str::random(120);
                $user->update(['api_token'=> $token]);

                return response()->json([
                    'name' => $user->name,
                    'code' => $user->code,
                    'api_token' => $token,
                ]);

            }else {
                return response()->json([
                    'message' => 'Hatalı kullanıcı adı ya da şifre!'
                ],401);
            }
        }else {
            return response()->json([
                'message' => 'Hatalı kullanıcı adı ya da şifre!'
            ],401);
        }
    }
}
