<?php


namespace App\Http\Middleware;
use Closure;
use \Auth;

class DynamicTypeCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

                    $types = array_slice(func_get_args(), 2); // [default, admin, manager]

                    $ul_count=0;
                    foreach (config('myconfig.userlevels') as $userlevel_single_key => $userlevel_single_value)
                    {
                      $userlevels[$ul_count]=new \stdClass();
                      $userlevels[$ul_count]->rolename=$userlevel_single_key;
                      $userlevels[$ul_count]->rolelevel=$userlevel_single_value;

                      $userlevelnames[]=$userlevel_single_key;
      
                      $ul_count++;
                    }


                    foreach ($types as $type) {

                        try {

                            if(!in_array($type,$userlevelnames)) // make sure we got a "real" type
                            throw new \Exception('Type not found');

                            if (Auth::user()->hasType($type)) {
                                return $next($request);
                            }

                        } catch (\Exception $exception) {

                            //dd('Could not find type ' . $type);

                        }
                    }

                    return redirect('notfound');
                }
}