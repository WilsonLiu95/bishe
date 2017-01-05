<?php

namespace App\Http\Controllers\Auth;

use App\Model\Admin;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Login extends Controller
{
    public function postIndex()
    {
        $account = request()->input("account");
        $password = request()->input("password");
        $admin = Admin::where("account",$account)->where("password",$password);
        if($admin->count()){
            session()->flush();
            session()->set("isLogin",true);
//            session()->put("info",$admin->get()[0]);
            $this->redirect['url'] = "admin";
           return response()->json($this->redirect);
        }
        $this->error["msg"] = "账号密码错误,请重试";
        return response()->json($this->error);
    }
}
