<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Model;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class Wechat extends Controller
{
    public function getIndex(Request $request)
    {
        $code = $request->query("code");
        // 请求中需要带上code,否则无法进行微信认证
        if (!isset($code)){
            return $this->toast(0,"code不存在");
        }
        
        $getUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . env("WE_APPID") ."&secret=" .
            env('WE_SECRET') . "&code=$code&grant_type=authorization_code";
        
        $client = new \GuzzleHttp\Client(['base_uri'=>'https://api.weixin.qq.com','verify' => false]); // 省去SSL的证书，防止某些机子没有SSL证书造成请求失败
        $res = $client->request('GET', $getUrl);
        $body = \GuzzleHttp\json_decode($res->getBody());


        $this->redirect['type'] = "url";
        $this->redirect['url'] = env('BASE_PATH') . "/#/register"; 
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
            session()->put("type",2);
            session()->put("id",$student->get()[0]["id"]);
            $this->redirect["session"] = session()->all();
            $this->redirect['url'] = env('BASE_PATH') . "/#/student/course";
            return response()->json($this->redirect);
        }
        $teacher = Model\Teacher::where("openid",$body->openid);
        if ($teacher->count()){
            session()->put("type",1);
            session()->put("id",$teacher->get()[0]["id"]);
            $this->redirect['url'] = env('BASE_PATH') . "/#/teacher/course";
            return response()->json($this->redirect);
        }

        // 该微信用户未注册
        $this->redirect['msg'] = "尚未注册";
        $this->redirect['type'] = "url";

        return response()->json($this->redirect);
    }
}
