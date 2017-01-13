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
        // 判断是否微信授权过
        if(request()->segment(1) =="admin"){
            if(session()->get("isLogin")){
                return $next($request);
            } else {
                $res_data = array(
                    "url" =>config()->get("config")["basepath"] . "login",
                    "state" => 301,
                    "type" => "url",
                );
                return response()->json($res_data);
            }

        }

        if (!session()->has("openid")){
            $wechat = config()->get("config")["wechat"];
            $res_data = array(
                "url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $wechat['appid'] . "&redirect_uri=". urlencode(config()->get("config")["basepath"] . '/wechat') . "&response_type=code&scope=snsapi_base#wechat_redirect",
                "state" => 301,
                "type" => "url",
            );
            return response()->json($res_data);
        }

        return $next($request);
    }
}
