<?php

namespace App\Http\Middleware;

use Closure;


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
//        if ($this->auth->guest()) {
//            if ($request->ajax()) {
//                return response('Unauthorized.', 401);
//            } else {
//                return redirect()->guest('auth/login');
//            }
//        }
        if (!$request->session()->get("name")){
//            $request->session()->set("name", "wilsonAuth");
//            return redirect("http://wilsonliu.cn");
        }

        return $next($request);
    }
}
