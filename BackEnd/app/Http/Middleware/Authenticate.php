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

        if ($request->path() =="login" && $request->isMethod("GET")){
            return $next($request);
        }
        if (!session()->has("openid")){
            $res_data = array(
                "url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4282c5fcbaa7e309&redirect_uri=http%3A%2F%2F192.168.2.1%3A8080%2F%23%2Fwechat&response_type=code&scope=snsapi_base#wechat_redirect",
                "state" => 301,
                "type" => "url",
                "session" => session()->all(),
            );
            return response()->json($res_data);
        }

        return $next($request);
    }
}
