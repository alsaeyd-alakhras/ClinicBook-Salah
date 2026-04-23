<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CheckUserCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            return $next($request);
        }
        // تحقق من وجود Cookie معين
        if (Cookie::has('user_id')) {
            if(Auth::check()){
                return $next($request);
            }
            $user = User::find(Cookie::get('user_id'));
            Auth::login($user);
        }else{
            return redirect()->route('login');
        }
        return $next($request);
    }
}
