<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class OperatorAuthenticate
{
    public function handle($request, Closure $next)
    {
        if ($request->is('api/*')) {
            // For API requests, check token
            $token = $request->bearerToken();
            if (!$token || !DB::table('operators')->where('api_token', $token)->exists()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            // Set session for compatibility
            $operator = DB::table('operators')->where('api_token', $token)->first();
            Session::put('operator_id', $operator->id);
            Session::put('operator_name', $operator->full_name);
            Session::put('role_id', $operator->role_id);
            Session::put('zila_id', $operator->zila_id);
            Session::put('tehsil_id', $operator->tehsil_id);
        } else {
            // For web requests, check session
            if (!Session::has('operator_id')) {
                return redirect()->route('login');
            }
        }
        return $next($request);
    }
}
