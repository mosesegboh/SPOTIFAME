<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;


class UsersController extends Controller
{
    private $settings;

    private $orderby;

    private $users=array();

    public function __construct()
    {

		$this->middleware('auth');


        $this->generateConfig();

	$this->settings=$this->getSettings();

	

	}

    public function getPage(Request $request)
    {


$ipAddress = $_SERVER['REMOTE_ADDR'];

		$userid = Auth::id();


        if ($request->input('pagenum'))
		$offset = ($request->input('pagenum')-1)*$this->settings['sp_perpage'];
		else
		$offset = '0';

        if($request->input('orderby') =='' || !$request->input('orderby'))
		$orderbyget='';
		else
        $orderbyget=$request->input('orderby');
        
        $this->orderby='t1.created_at DESC'; //default
        
        $namesearch=urldecode($request->input('name'));

        $usernamesearch=urldecode($request->input('username'));
        
        $emailsearch=urldecode($request->input('email'));

        if($request->input('advancedsearch')!='1')
        {

            $usernamesearch='';
            $emailsearch='';

        }

        $maintypesearch=$request->input('maintype');
        $subtypesearch=$request->input('subtype');
        if(config('myconfig.userlevels')[$maintypesearch]!='' && config('myconfig.userlevels')[$maintypesearch]<10)
        {
            $subtypesearch='';
        }


        $isartist=urldecode($request->input('isartist'));
        $islabel=urldecode($request->input('islabel'));
        $ismanager=urldecode($request->input('ismanager'));
        $isplaylistowner=urldecode($request->input('isplaylistowner'));

        $isjournalist=urldecode($request->input('isjournalist'));
        $isdjremixer=urldecode($request->input('isdjremixer'));

        $generated=urldecode($request->input('generated'));
        $verified=urldecode($request->input('verified'));

        $ul_count=0;
              foreach (config('myconfig.userlevels') as $userlevel_single_key => $userlevel_single_value)
              {
                $userlevels[$ul_count]=new \stdClass();
                $userlevels[$ul_count]->rolename=$userlevel_single_key;
                $userlevels[$ul_count]->rolelevel=$userlevel_single_value;

                $ul_count++;
              }


        $theresultset = 
					DB::table('users as t1')
                    ->select('t1.*')
                    ->where(function($query) use ($isartist)
                                 {
							if ($isartist!='') {
										$query->where('t1.isartist','=',$isartist);
							                    }	
                                })
                    ->where(function($query) use ($islabel)
                                {
                           if ($islabel!='') {
                                       $query->where('t1.islabel','=',$islabel);
                                               }	
                               })
                    ->where(function($query) use ($ismanager)
                               {
                          if ($ismanager!='') {
                                      $query->where('t1.ismanager','=',$ismanager);
                                              }	
                              })
                    ->where(function($query) use ($isplaylistowner)
                              {
                         if ($isplaylistowner!='') {
                                     $query->where('t1.isplaylistowner','=',$isplaylistowner);
                                             }	
                             })
                    ->where(function($query) use ($isjournalist)
                             {
                        if ($isjournalist!='') {
                                    $query->where('t1.isjournalist','=',$isjournalist);
                                            }	
                            })
                    ->where(function($query) use ($isdjremixer)
                              {
                         if ($isdjremixer!='') {
                                     $query->where('t1.isdjremixer','=',$isdjremixer);
                                             }	
                             })
                    ->where(function($query) use ($generated)
                             {
                         if ($generated!='') {
                                     $query->where('t1.generated','=',$generated);
                                             }	
                         })
                    ->where(function($query) use ($verified)
                             {
                         if ($verified!='') {
                                    if($verified)
                                    $query->whereNotNull('t1.email_verified_at');
                                    else
                                    $query->whereNull('t1.email_verified_at');
                                             }	
                         })
                    ->where(function($query) use ($maintypesearch,$subtypesearch,$userlevels)
						{
							if ($maintypesearch!='') {
                                if(config('myconfig.userlevels')[$maintypesearch]!='' 
                                && config('myconfig.userlevels')[$maintypesearch]<10)
                                {
                                $query->where('t1.type','=', $maintypesearch);
                                }
                                else
                                {
                                    
                                    if($subtypesearch!='')
                                    $query->where('t1.type','LIKE', '%'.$subtypesearch.'%');
                                    else
                                    {
                                        foreach($userlevels as $userlevels_s)
                                        {
                                            if($userlevels_s->rolelevel>=10)
                                            $query->orWhere('t1.type','=', $userlevels_s->rolename);
                                        }


                                    }
                                }
							}
                        })
                    ->where(function($query) use ($namesearch)
						{
							if ($namesearch!='') {
								$query->where('t1.name','LIKE', '%'.$namesearch.'%');
							}
                        })
                    ->where(function($query) use ($usernamesearch)
						{
							if ($usernamesearch!='') {
								$query->where('t1.username','LIKE', '%'.$usernamesearch.'%');
							}
                        })
                    ->where(function($query) use ($emailsearch)
						{
							if ($emailsearch!='') {
								$query->where('t1.email','LIKE', '%'.$emailsearch.'%');
							}
                    })
					->orderByRaw($this->orderby)
					->offset($offset)
					->limit($this->settings['sp_perpage'])
					->get();
					

							$s_c=0;
					foreach($theresultset as $theresultset_s)
								{

                                    $theresultset[$s_c]=$theresultset_s;
                                    
                                    $theresultset[$s_c]->userlevels=explode('|',$theresultset_s->type);

                                    $theresultset[$s_c]->isuser=0;
                                    foreach ($theresultset[$s_c]->userlevels as $userl_s)
                                    {
                                        if(config('myconfig.userlevels')[$userl_s]>=10)
                                        {
                                            $theresultset[$s_c]->isuser=1;
                                        break;
                                        }

                                    }
                                    

									$s_c++;
								}

	$this->users=$theresultset;

								
								$item_count = 
                                DB::table('users as t1')
                                ->where(function($query) use ($isartist)
                                 {
                                    if ($isartist!='') {
                                                $query->where('t1.isartist','=',$isartist);
                                                        }	
                                        })
                                    ->where(function($query) use ($islabel)
                                                {
                                        if ($islabel!='') {
                                                    $query->where('t1.islabel','=',$islabel);
                                                            }	
                                        })
                                    ->where(function($query) use ($ismanager)
                                            {
                                        if ($ismanager!='') {
                                                    $query->where('t1.ismanager','=',$ismanager);
                                                            }	
                                        })
                                    ->where(function($query) use ($isplaylistowner)
                                            {
                                        if ($isplaylistowner!='') {
                                                    $query->where('t1.isplaylistowner','=',$isplaylistowner);
                                                            }	
                                        })
                                    ->where(function($query) use ($isjournalist)
                                        {
                                   if ($isjournalist!='') {
                                               $query->where('t1.isjournalist','=',$isjournalist);
                                                       }	
                                       })
                                    ->where(function($query) use ($isdjremixer)
                                         {
                                    if ($isdjremixer!='') {
                                                $query->where('t1.isdjremixer','=',$isdjremixer);
                                                        }	
                                        })
                                    ->where(function($query) use ($generated)
                                        {
                                    if ($generated!='') {
                                                $query->where('t1.generated','=',$generated);
                                                        }	
                                    })
                                    ->where(function($query) use ($verified)
                                        {
                                    if ($verified!='') {
                                            if($verified)
                                            $query->whereNotNull('t1.email_verified_at');
                                            else
                                            $query->whereNull('t1.email_verified_at');
                                                        }	
                                    })
                                ->where(function($query) use ($maintypesearch,$subtypesearch,$userlevels)
                                {
                                    if ($maintypesearch!='') {
                                        if(config('myconfig.userlevels')[$maintypesearch]<10)
                                        $query->where('t1.type','=', $maintypesearch);
                                        else
                                        {
                                            if($subtypesearch!='')
                                            $query->where('t1.type','=', $subtypesearch);
                                            else
                                            {
                                                foreach($userlevels as $userlevels_s)
                                                {
                                                    if($userlevels_s->rolelevel>=10)
                                                    $query->orWhere('t1.type','=', $userlevels_s->rolename);
                                                }


                                            }
                                        }
                                    }
                                })
                                ->where(function($query) use ($namesearch)
                                        {
                                            if ($namesearch!='') {
                                                $query->where('t1.name','LIKE', '%'.$namesearch.'%');
                                            }
                                        })
                                    ->where(function($query) use ($usernamesearch)
                                        {
                                            if ($usernamesearch!='') {
                                                $query->where('t1.username','LIKE', '%'.$usernamesearch.'%');
                                            }
                                        })
                                    ->where(function($query) use ($emailsearch)
                                        {
                                            if ($emailsearch!='') {
                                                $query->where('t1.email','LIKE', '%'.$emailsearch.'%');
                                            }
                                    })
									->count();


            

                                    
		
		$meta=array(

		'title' => 'Users - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Users - Admin Panel',

		'keywords' => '',

	);

        $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}

		

		return view('admin/users', [
		'users'=>$this->users,
		'meta' => $meta,
		'item_count'=> number_format($item_count),
		'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
		'pagination'=>$pagination,
        'paginationdisplay'=>$paginationdisplay,
        'userlevels'=>$userlevels,
		]);



	}
	
	
	
}
