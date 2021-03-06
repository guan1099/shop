<?php

namespace App\Http\Middleware;

use App\Model\UserModel;
use Closure;
use Illuminate\Support\Facades\Redis;

class CheckToken
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
        if(isset($_COOKIE['uid'])&&isset($_COOKIE['token'])){
            $redis_token='redis_token_str:'.$_COOKIE['uid'].'';
            if(Redis::hGet($redis_token,'pc')==$_COOKIE['token']){
                $request->attributes->add(['is_login'=>1]);
            }else{
                $request->attributes->add(['is_login'=>0]);
            }
        }else{
            $request->attributes->add(['is_login'=>0]);
        }
        return $next($request);
    }
}
