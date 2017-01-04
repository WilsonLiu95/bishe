<?php

namespace App\Http\Middleware;

use Closure;
use App\Model;

class Authenticate
{

    public function __construct()
    {

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 先判断是否微信授权过

        if ($request->path() =="login" && !$request->isMethod("POST")){
            return $next($request);
        }

        if (!session()->has("openid")){
            $res_data = array(
                "url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4282c5fcbaa7e309&redirect_uri=http%3A%2F%2F192.168.2.1%3A8080%2F%23%2Fwechat&response_type=code&scope=snsapi_base#wechat_redirect",
                "state" => 301,
                "type" => "url",
                "session" => session()->all(),
                "sessionid" => session()->getid(),
            );

            return response()->json($res_data);
        }
//        $openid = $request->session()->get("openid");

//        if ($request->path() =="login" && $request->isMethod("POST")){
//            return $next($request);
//        }

//        // 再判断该用户是否识别过身份
//        if (!$request->session()->has("type")){
//            $isRegister = 0;
//            $student = Model\Student::where("openid",$openid);
//            if ($student->count()){
//                $request->session()->put("type","student");
//                $request->session()->put("info",$student->all());
//                $isRegister = 1;
//            }
//            $teacher = Model\Teacher::where("openid",$openid);
//            if ($teacher->count()){
//                $request->session()->put("type","teacher");
//                $request->session()->put("info",$teacher->all());
//                $isRegister = 1;
//            }
//            if (!$isRegister){
//                $res_data = array(
//                    "url" =>"login",
//                    "state" => "301",
//                    "msg" => "未注册"
//                );
//                return response()->json($res_data);
//            }
//
//        }

        return $next($request);
    }
}
