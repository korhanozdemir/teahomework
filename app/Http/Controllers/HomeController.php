<?php

namespace App\Http\Controllers;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard by user type.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $role =Auth::user()->getRole();
        return view($role);
    }
}
