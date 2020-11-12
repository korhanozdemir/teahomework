<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function index(){
        return Setting::where("name", "version")->orderBy('id', 'desc')->first("value");
    }
}
