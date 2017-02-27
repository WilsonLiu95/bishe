<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model;
class Register extends Controller
{
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

            $this->redirect['msg'] = '您已经注册过,请勿重复注册,即将为您跳转';
        }else if($isExistStudent->exists() || $isExistTeacher->exists()){

            $isTeacher = $isExistTeacher->exists();
            $user = $isTeacher ?   $isExistTeacher->first() : $isExistStudent->first();
            // 如果存在,则更新openid
            $user->update([
                "phone" => $request->phone,
                "openid" => $sid]);
        }else{
            return $this->toast(0,"不存在该账户,请确认姓名与学号");
        }
        $this->redirect['url'] = $isTeacher ? "teacher":"student";
        session()->put("type", $isTeacher? 1:2);
        session()->put("id",$user["id"]);
        return response()->json($this->redirect);
    }
}
