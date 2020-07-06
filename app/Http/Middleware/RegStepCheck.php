<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Redirect;

class RegStepCheck
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
            return $next($request);
        }
        else
        {
            return redirect()->route('regsteps.step'.auth()->user()->regStep());
            //Redirect::route('regsteps.step'.auth()->user()->regStep());

        }

    }
}