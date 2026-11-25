<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        //if (Auth::user()->role_id != 1) {
        if (empty(Auth::user()->role_id)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);

        //abort_if(! Auth::check(), 401, 'Unauthorized access');
        //return $next($request);
        //return redirect()->route('login')->with('error', 'You do not have admin access.');
    }
}
