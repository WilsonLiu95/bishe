<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model;
class Register extends Controller
{
    public function postIndex(Request $request)
    {
        $isExist = 0;

        if (Model\Student::where("student_num", $request->student_num)->where("name", $request->name)->count()) {
            $isExist = 1;
            $user = Model\Student::where("student_num", $request->student_num)
                ->where("name", $request->name);
            $request->session()->put("type", 2);
            $this->redirect['url'] = "student";
        } else if (Model\Teacher::where("teacher_num", $request->student_num)->where("name", $request->name)->count()) {
            $isExist = 1;

            $user = Model\Teacher::where("teacher_num", $request->student_num)
                ->where("name", $request->name);
            $request->session()->put("type",1); // admin类型为0,老师类型为1,学生的类型为2
            $this->redirect['url'] = "teacher";
        };
        // 不存在对应的账号
        if (!$isExist) {
            $this->error['msg'] = "不存在该账户,请确认姓名与学号";
            return response()->json($this->error);
        }

        // 如果存在,则更新openid
        $user->update(["phone" => $request->phone,
            "openid" => $request->session()->get("openid")]);
        session()->put("id",$user->get()[0]["id"]);


        $this->redirect["data"] = session()->all();
        return response()->json($this->redirect);
    }
}
