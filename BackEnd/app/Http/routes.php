<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'auth'], function(){

    Route::controller("/register","Auth\Register");
    Route::controller("/account","Api\Account");

    Route::group(['prefix' => 'admin'], function(){
        // 管理员的接口走这里

    });
});
Route::controller("/course","Api\Course");

Route::controller("/test","Test");

Route::controller("/login","Auth\Login");
Route::controller("/wechat","Auth\Wechat");

