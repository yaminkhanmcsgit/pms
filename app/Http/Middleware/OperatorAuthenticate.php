<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class OperatorAuthenticate
{
    public function handle($request, Closure $next)
    {
        if (!Session::has('operator_id')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
