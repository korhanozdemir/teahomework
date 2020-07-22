<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/


Route::post('/images/upload/{guid}','Media\ImageController@uploadImages')->middleware('api-token');
Route::post('/login','Api\Teacher\AuthController@login');

Route::post('/teacher/homework/add','Api\Teacher\CreateHomeworkController@index')->middleware('api-token');
Route::get('/teacher/get-class-level','Api\Teacher\IndexController@getClassLevel');
Route::get('/teacher/get-course-list/{classID}','Api\Teacher\IndexController@getCourseList');
Route::get('/teacher/get-lecture-list/{courseID}','Api\Teacher\IndexController@getLectureList');
Route::get('/teacher/get-sublecture-list/{lectureID}','Api\Teacher\IndexController@getSubLectureList');
Route::get('/teacher/get-student-list/{level}','Api\Teacher\IndexController@getStudentList')->middleware('api-token');


