<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsForceTransactionMiddleware
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
        if(Auth::user()->is_force_transaction()){
            $notification = notification('عذرا: لقد تخطيت الحد الأقصي لصندوق المندوب قبل سياسة أخر وقت للإيداع، يتوجب عليك انشاء تحويل مالي من الصندوق.', false);
            return redirect(route('transactions.create'))->with($notification);
        }
        return $next($request);
    }
}
