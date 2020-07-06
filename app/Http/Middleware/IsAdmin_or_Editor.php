<?php

namespace App\Http\Middleware;
use Closure;

class IsAdmin_or_Editor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->user()->isAdmin() || auth()->user()->isEditor()) {
            return $next($request);
        }
        return redirect('notfound');
    }
}