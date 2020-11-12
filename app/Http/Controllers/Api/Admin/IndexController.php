<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class IndexController extends Controller
{
    public function index(){

        //Password Hash Func

        return 1;
        $users =  User::where("role", 3)
            ->where('password', 'not like', "$%")
                ->get();

        foreach ($users as $user) {
            $pass = $user->getPassword();

            $user->password = Hash::make($pass);
            $user->save();
        }
        return 1;


    }
}
