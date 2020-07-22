<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);


Route::middleware(['auth'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('images/{parameters?}', 'Media\ImageController@downloadImages')->where('parameters', '.*');
});


Route::group(['namespace'=> 'Api', 'prefix'=> 'api', 'as'=>'api', 'middleware' => 'auth:web'],function (){

    Route::group(['namespace'=> 'Student', 'prefix'=> 'student', 'as'=>'student', 'middleware' => 'student'],function (){
        Route::get('/get-students', 'indexController@index')->name('index');
        Route::get('/get-student-details', 'IndexController@getStudentDetails')->name('getStudentDetails');
        Route::get('/get-homework/{id}', 'indexController@getHomework')->name('GetStudentHomework');


        Route::get('/homework-student-results/{homework_id}/{question_id}', 'HomeworkStudentResultController@getQuestionResults')->name('GetStudentQuestionAnswer');
        Route::post('/homework-student-results/{homework_id}/{question_id}', 'HomeworkStudentResultController@updateQuestionResults')->name('PostStudentQuestionAnswer');

    });

    Route::group(['namespace'=> 'Teacher', 'prefix'=> 'teacher', 'as'=>'teacher', 'middleware' => 'teacher'],function (){
        Route::get('/get', 'indexController@index')->name('index');
    });

    Route::group(['namespace'=> 'Admin', 'prefix'=> 'admin', 'as'=>'admin', 'middleware' => 'admin'],function (){
        Route::get('/get', 'indexController@index')->name('index');
        Route::apiResources([
            'users' => 'UserController'
        ]);
    });

});



