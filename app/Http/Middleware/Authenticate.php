<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Request;
class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {   
        // var_dump('ZZZ');
        // dd(Auth::guard($guard)->guest());
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }

        // if(Auth::user()->status==9){
        //     return response()->view('layouts.authfall');
        // }
        // if(Auth::user()->customer==null){
        //      return redirect()->guest('logout');
        // }

        // if(Auth::user()->customer->status==0){
        //     return response()->view('layouts.msg',['msg'=>'該伺服器已停用,請洽管理員']);
        // }


        // $path = $request->path();
        // $log = new \App\models\Usetrackers;
        // $log->path = $path;
        // $log->ip_address=Request::ip();
        // $log->user_id = Auth::user()->email;
        // $log->save();
        return $next($request);
    }
}
