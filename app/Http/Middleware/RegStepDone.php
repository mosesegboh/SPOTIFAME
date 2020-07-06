<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Redirect;

class RegStepDone
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
       
        if(auth()->user()->regStep()=='1') {
                return redirect()->route('admin.home');
        }
        else
        {
            
            return $next($request);

        }

    }
}