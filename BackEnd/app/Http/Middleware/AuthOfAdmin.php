<?php

namespace App\Http\Middleware;

use Closure;
use App\Model;

class AuthOfAdmin
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

        // 如果是PC管理端，第一段URL需要为admin
        if(request()->segment(1) =="admin"){
            if(session()->get("isLogin")){
                return $next($request);
            } else {
                $res_data = array(
                    "url" =>env('BASE_PATH') . "login",
                    "state" => 301,
                    "type" => "url",
                );
                return response()->json($res_data);
            }
        }

        return $next($request);
    }
}
