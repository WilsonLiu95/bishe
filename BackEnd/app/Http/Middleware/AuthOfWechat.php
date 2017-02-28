<?php

namespace App\Http\Middleware;

use Closure;
use App\Model;

class AuthOfWechat
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

        if (!session()->has("openid")){
            $res_data = array(
                "url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . env('WE_APPID') . "&redirect_uri=". urlencode(env('BASE_PATH') . '/#/wechat') . "&response_type=code&scope=snsapi_base#wechat_redirect",
                "state" => 301,
            );
            return response()->json($res_data);
        }

        return $next($request);
    }
}
