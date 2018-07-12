<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RedirectIfAuthenticated
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

        if(isset($_GET['account'])){
            $account = User::where('phone',$_GET['account'])->first();
            setcookie('user_id',$account->id,(time()+3600));
            setcookie('login_type',$account->type,(time()+3600));
            setcookie('user_name',$account->name,(time()+3600));
            setcookie('user_type','channel',(time()+3600));
        }else{
            if(!isset($_COOKIE['login_type'])){
                return redirect(('/login'))->withErrors('需要登陆您的账号才能完成购买');
                if(!Auth::check()){
                    return redirect('/logout');
                }
            }
        }
        return $next($request);
    }
}
