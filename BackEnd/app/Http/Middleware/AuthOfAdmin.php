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
            if(session()->get("isLogin")){
                return $next($request);
            } else {
                $res_data = array(
                    "url" =>env('BASE_PATH') . "/#/login",
                    "state" => 301,
                );
                return response()->json($res_data);
        }

        return $next($request);
    }
}
