<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model;
class Register extends Controller
{
    use BaseTrait;
    public function postIndex(Request $request)
    {
        $sid = session()->get("openid");

        // 是否已经绑定过
        $hasRegisterStudent = Model\Student::where('openid',$sid);
        $hasRegisterTeacher =  Model\Teacher::where('openid',$sid);

        // 是否存在该用户信息
        $isExistStudent = Model\Student::where("job_num", $request->job_num)->where("name", $request->name);
        $isExistTeacher =  Model\Teacher::where("job_num", $request->job_num)->where("name", $request->name);

        if($hasRegisterStudent->exists() || $hasRegisterTeacher->exists()){
            // 如果已注册绑定过
            $isTeacher = $hasRegisterTeacher->exists();
            $user = $isTeacher ?  $hasRegisterTeacher->first():$hasRegisterStudent->first() ;

            $msg = '您已经注册过,请勿重复注册,即将为您跳转';
        }else if($isExistStudent->exists() || $isExistTeacher->exists()){

            $isTeacher = $isExistTeacher->exists();
            $user = $isTeacher ?   $isExistTeacher->first() : $isExistStudent->first();
            // 如果存在,则更新openid
            $user->update([
                "phone" => $request->phone,
                "openid" => $sid]);
            $msg = "登录成功，自动为您跳转";
        }else{
            return $this->toast(0,"不存在该账户,请确认姓名与学号");
        }
        // 注入session
        session()->put("isLogin", true);
        session()->put("isTeacher", $isTeacher);
        session()->put("id",$user["id"]);

        return $this->redirect($isTeacher ? "teacher":"student",false,$msg);
    }
    public function getIsLogin(){
        if($this->getSessionInfo("isLogin")){
            return $this->redirect($this->isTeacher()?'teacher':'student');
        }else{
            return "未登录";
        }

    }
}
