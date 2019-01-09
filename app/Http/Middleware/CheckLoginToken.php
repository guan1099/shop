<?php

namespace App\Http\Middleware;

use Closure;

class CheckLoginToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->session()->get('u_token')){
        header('refresh:2;url=/userlogin');
        die('请登录');
        }
        if(empty($_COOKIE['uid'])){
            echo "请先登录";
            header('refresh:1;url=/userlogin');
            die;
        }else if($_COOKIE['token']!=$request->session()->get('u_token')){
            die('非法登录');
        }

        return $next($request);
    }
}
