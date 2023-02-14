<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()->session){
            auth()->user()->session->update(['last_activity'=>time()]);
        }
        return $next($request);
    }
}
