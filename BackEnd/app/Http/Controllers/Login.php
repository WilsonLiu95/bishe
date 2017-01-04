<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Psy\Util\Json;

class Login extends Controller
{
    public function getIndex(Request $request)
    {
        $code = $request->query("code");
        // 请求中需要带上code,否则无法进行微信认证
        if (!isset($code)){
            $this->error['msg'] = "code不存在";
            return response()->json($this->error);
        }

        $wechat = config()->get("config")["wechat"];
        $getUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $wechat['appid'] ."&secret=" .
                $wechat['secret'] . "&code=$code&grant_type=authorization_code";
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $getUrl);
        $body = \GuzzleHttp\json_decode($res->getBody());


        $this->redirect['type'] = "url";
        $this->redirect['url'] = "http://192.168.2.1:8080/#/login";
        if (isset($body->errcode)){
            // 说明验证码无效
            $this->redirect['msg'] = "验证码无效";
            return response()->json($this->redirect);
        }
        // 微信授权成功
        session()->put("access_token",$body->access_token);
        session()->put("refresh_token",$body->refresh_token);
        session()->put("openid",$body->openid);

        $student = Model\Student::where("openid",$body->openid);

        if ($student->count()){
            session()->put("type","student");
            session()->put("info",$student->get()[0]);
            $this->redirect['url'] = "http://192.168.2.1:8080/#/student/course";
            return response()->json($this->redirect);
        }
        $teacher = Model\Teacher::where("openid",$body->openid);
        if ($teacher->count()){
            session()->put("type","teacher");
            session()->put("info",$teacher->get()[0]);

            $this->redirect['url'] = "http://192.168.2.1:8080/#/teacher/course";
            return response()->json($this->redirect);
        }

        // 该微信用户未注册
        $this->redirect['msg'] = "尚未注册";
        $this->redirect['type'] = "url";

        return response()->json($this->redirect);
    }
    public function postIndex(Request $request)
    {
        $isExist = 0;

        if (Model\Student::where("student_num", $request->student_num)->where("name", $request->name)->count()) {
            $isExist = 1;
            $user = Model\Student::where("student_num", $request->student_num)
                ->where("name", $request->name);
            $request->session()->put("type", "student");

        } else if (Model\Teacher::where("teacher_num", $request->student_num)->where("name", $request->name)->count()) {
            $isExist = 1;

            $user = Model\Teacher::where("teacher_num", $request->student_num)
                ->where("name", $request->name);
            $request->session()->put("type","teacher");
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
        session()->put("info",$user->get()[0]);
        $this->redirect['url'] = session()->get("type");
        $this->redirect["data"] = session()->all();
        return response()->json($this->redirect);


    }

}
