<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JwtSetMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //https://stackoverflow.com/questions/55727972/how-to-append-authorization-header-in-laravel-5-8
        if (Session::has('jwt-token'))
            $request->headers->add(['Authorization' => 'Bearer ' . Session::get('jwt-token')]);

        return $next($request);
    }
}
