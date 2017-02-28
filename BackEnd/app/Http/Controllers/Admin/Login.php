<?php

namespace App\Http\Controllers\Admin;

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
			return $this->redirect(['name'=>"admin"],'登陆成功');
		}
		return $this->toast(0,"账号密码错误,请重试");
	}
}
