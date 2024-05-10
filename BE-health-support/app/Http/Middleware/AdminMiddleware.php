<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {

    //     if ($request->session()->has('user')) {
    //         $user = $request->session()->get('user');
    //         if ($user->role === 'admin') {
    //             return $next($request);
    //         }
            
    //     }

    //     return redirect('/login')->withErrors('Access Denied. You are not authorized to access this page.');
    // }
    

//     public function handle($request, Closure $next)
// {
//     if ($request->session()->has('user')) {
//         $user = $request->session()->get('user');
//         // dd($user); // Debug session user data

//         if ($user['role'] !== 'admin') {
//             return $next($request);
//         }
//     }

//     return redirect('/login')->withErrors('Access Denied. You are not authorized to access this page.');
// }
}
