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
    Route::group(['prefix' => 'wechat'], function(){
        // 微信接口 如下
        Route::controller("/register","Wechat\Register");
        Route::controller("/account","Wechat\AccountTab");
        Route::controller("/course","Wechat\CourseTab");
        Route::controller("/schedule","Wechat\ScheduleTab");
        Route::controller("/detail","Wechat\Detail");
    });

    Route::group(['prefix' => 'admin'], function(){
        // 管理员的接口走这里

    });
});

Route::controller("admin/login","Admin\Login"); // 管理端登录

Route::controller("/wechat","Wechat\Wechat"); // 微信授权


Route::controller("/test","Test");