<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

use GuzzleHttp\Client;


class SpotifyAccountsController extends Controller
{
    
    private $settings;

    public function __construct()
    {

		$this->middleware('auth');


        $this->generateConfig();

	$this->settings=$this->getSettings();


	}


    public function grantSpotifyAccess(Request $request)
    {
        
        if($request->input('code')!='')
        $this->spotifyapi=SpotifyHelper::instance()->grantAccessToEverything(trim($request->input('code')));
        else
        $this->spotifyapi=SpotifyHelper::instance()->grantAccessToEverything();
        

    }
	
    public function getPage(Request $request)
    {

        
        
$ipAddress = $_SERVER['REMOTE_ADDR'];


        $userid = Auth::id();
        
        if($request->input('userid') && Auth::user()->isAdmin())
        $userid=$request->input('userid');

        if($request->input('username'))
        $username=$request->input('username');
        

        $accounttype=$request->input('accounttype');
        $statetype=$request->input('statetype');

        $country=$request->input('country');
     


        $artistsearch=urldecode($request->input('artistsearch'));
        
        if($artistsearch!='')
            {
                $itemresponse=array();
                $itemresponse=SpotifyHelper::instance()->getSpotifyArtistItemId($artistsearch);
                if($itemresponse['id']!='')
                $artistsearch=$itemresponse['id'];
                
            }
           
        $managersearch=urldecode($request->input('managersearch'));
            if($managersearch!='')
                {
                    $itemresponse=array();
                    $itemresponse=SpotifyHelper::instance()->getSpotifyUserItemId($managersearch);
                    if($itemresponse['id']!='')
                    $managersearch=$itemresponse['id'];
                    
                }

        $artistidsearch=[];
        if($artistsearch!='')
                {

                    $isfulltextsearch=1;

                    $fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($artistsearch,1);
                    
                    $get_results = 
                    DB::table('spotify_items AS t1')
                    ->select('t1.*')
                    ->where(function($query) use ($artistsearch,$isfulltextsearch)
                            {
                                if ($artistsearch!='' && $isfulltextsearch!='1') {
                                    $query->where('t1.name','=', $artistsearch)
                                    ->orWhere('t1.name', 'LIKE', '%'.$artistsearch.'%');
                                }
                            })
                        ->where(function($query) use ($artistsearch,$isfulltextsearch,$fulltextsearchstring)
                            {
                                if ($artistsearch!='' && $isfulltextsearch=='1') {
                                    $query->where('t1.name','=', $artistsearch)
                                    ->orWhereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
                                }
                            })
                    ->where('t1.type', '=', 'artist')
                    ->get();
        
                    foreach($get_results as $get_results_s)
                                {
                 $artistidsearch[]= $get_results_s->itemid;
                                }
       
                                if(empty($artistidsearch))
                      return $this->returnEmpty($request);
                              
                     
                    
                }

                
                        

        if ($request->input('pagenum'))
		$offset = ($request->input('pagenum')-1)*$this->settings['sp_perpage'];
		else
		$offset = '0';
        
    
        $notconnectedaccounts=array();
        $notconnectedaccounts = 
        DB::table('spotify_accounts_auth as t1')
        ->select('t1.*')
        ->where('t1.state', '=', '10')
        ->where('t1.userid','=',$userid)
        ->get();

        if(!empty($notconnectedaccounts))
        {
        $s_c=0;
        foreach($notconnectedaccounts as $notconnectedaccounts_s)
								{

          $notconnectedaccounts[$s_c]=$notconnectedaccounts_s;
					$s_c++;
                                }
         }


         $isfulltextsearch=1;
        $fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($managersearch,1);

        $theresultset = 
					DB::table('spotify_accounts_auth as t1')
                    ->select('t1.*','t2.username','t2.type','t3.artistpick','t3.artistpickstate','t3.id AS realartistid','t3.spid AS artistid','t3.active AS artistactive','t4.name AS artistname','t4.url AS artisturl','t4.id AS mydbid')
                    ->leftJoin('users AS t2', function($join)
							{
							$join->on('t2.id', '=', 't1.userid');
                            })
                     ->leftJoin('spotify_accounts_auth_realartists AS t3', function($join)
							{
                            $join->on('t1.artistconnectid', '=', 't3.id');
                            })
                      ->leftJoin('spotify_items AS t4', function($join)
							{
                            $join->on('t3.spid', '=', 't4.itemid');
                            $join->where('t4.type','=', 'artist');
                            })
                    ->where(function($query) use ($userid,$accounttype) {
                            if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {

                                    if(Auth::user()->isAssistant())
                                    {
                                        if($accounttype=='')
                                        {
                                            $query->where('t1.userid','=',$userid);
                                        }
                                        elseif($accounttype=='assistant')
                                        {
                                            $query->where('t1.userid','!=',$userid);
                                            $query->where('t1.assistantcreated','=','1');
                                        }
                                         
                                    }
                                    else
                                    $query->where('t1.userid','=',$userid);
                                }
                                else
                                {
                                    if($accounttype=='')
                                    {
                                        $query->where('t1.userid','=',$userid);

                                    }
                                    elseif($accounttype=='all')
                                    {
                                        
                                    }
                                    elseif($accounttype=='generated')
                                    {
                                        $query->where('t1.userid','!=',$userid);
                                        $query->where('t1.generated','=','1');
                                    }
                                    elseif($accounttype=='assistant')
                                    {
                                        $query->where('t1.userid','!=',$userid);
                                        $query->where('t1.assistantcreated','=','1');
                                    }

                                }
                            })
                            
                    ->where(function($query) use ($country) {
                                if ($country!='') {
                                           $query->where('t1.country','=', $country);
                                                    }	
                        })
                     ->where(function($query) use ($statetype) {
                        if ($statetype!='') {
                                   if(in_array($statetype,array('0','1','10')))
                                   $query->where('t1.state','=', $statetype);
                                            }	
                        })
                    ->where(function($query) use ($artistidsearch)
                        {
                                if (!empty($artistidsearch)) {
                               $query->whereIn('t3.spid', $artistidsearch);
                               
                                       }	
                       })
                    ->where(function($query) use ($managersearch,$isfulltextsearch)
                           {
                                        if ($managersearch!='' && $isfulltextsearch!='1') {
                                            $query->where('t1.spid','=',$managersearch)
                                            ->orWhere('t1.displayname', 'LIKE', '%'.$managersearch.'%');
                            }
                          })
                    ->where(function($query) use ($managersearch,$isfulltextsearch,$fulltextsearchstring)
                          {
                                        if ($managersearch!='' && $isfulltextsearch=='1') {
                                            $query->where('t1.spid','=',$managersearch)
                                            ->orWhereRaw("MATCH (displayname) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
                          }
                        })
					->orderByRaw('t1.id DESC')
					->offset($offset)
					->limit($this->settings['sp_perpage'])
                    ->get();

                    $s_c=0;
					foreach($theresultset as $theresultset_s)
								{
                                    
                                    if($theresultset_s->artistpick!='')
                                    {
                                        $realidtocheck=$theresultset_s->artistpick;
		                                $checkitem=SpotifyHelper::instance()->getSpotifyItemId($realidtocheck);
                                        if(!in_array($checkitem['type'],array('playlist','album','track','show','episode')) || $checkitem['id']=='')
                                        {

                                        }
                                        else
                                        {
                                            $realidtocheck=$checkitem['id'];
                                        }

                                        $get_item = DB::table('spotify_items AS t1')
                                        ->select('t1.imageurl AS artistpickimage','t1.name AS artistpickname')
                                        ->where('t1.itemid', '=', $realidtocheck)
                                        ->limit(1)
                                        ->get();
                                        foreach ($get_item as $row_itemget) {
                                            if($row_itemget->artistpickimage!='')
                                            {
                                                $theresultset_s->artistpickimage = $row_itemget->artistpickimage;
                                                $theresultset_s->artistpickname = $row_itemget->artistpickname;
                                            }
                                            
                                        }
                                    }
                                    
                                    

                                    
                                    $theresultset_s->roles=explode('|',$theresultset_s->type);


                                    $theresultset_s->playlists=array();
                                    if($theresultset_s->playlistcount>0)
                                    {


                                        $pl_get = DB::table('spotify_accounts_auth_realplaylists AS t1')
                                        ->select('t2.name AS playlistname','t2.url AS playlisturl')
                                        ->leftJoin('spotify_items AS t2', function($join)
                                        {
                                        $join->on('t1.spid', '=', 't2.itemid');
                                        })
                                        ->where('t1.managerid', '=', $theresultset_s->id)
                                        ->limit(5)
                                        ->get();

					
                                        foreach ($pl_get as $row) {
                                            $theresultset_s->playlists[] = $row;
                                        }
                                        

                                    }

                                    $theresultset[$s_c]=$theresultset_s;
									$s_c++;
                                }

                    $this->allresults=$theresultset;

                    

                    $item_count = 
                                DB::table('spotify_accounts_auth as t1')
                                ->leftJoin('spotify_accounts_auth_realartists AS t3', function($join)
                                {
                                $join->on('t1.artistconnectid', '=', 't3.id');
                                })
                                ->where(function($query) use ($userid,$accounttype) {
                                    if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
        
                                            if(Auth::user()->isAssistant())
                                            {
                                                if($accounttype=='')
                                                {
                                                    $query->where('t1.userid','=',$userid);
                                                }
                                                elseif($accounttype=='assistant')
                                                {
                                                    $query->where('t1.userid','!=',$userid);
                                                    $query->where('t1.assistantcreated','=','1');
                                                }
                                                 
                                            }
                                            else
                                            $query->where('t1.userid','=',$userid);
                                        }
                                        else
                                        {
                                            if($accounttype=='')
                                            {
                                                $query->where('t1.userid','=',$userid);
        
                                            }
                                            elseif($accounttype=='all')
                                            {
                                                
                                            }
                                            elseif($accounttype=='generated')
                                            {
                                                $query->where('t1.userid','!=',$userid);
                                                $query->where('t1.generated','=','1');
                                            }
                                            elseif($accounttype=='assistant')
                                            {
                                                $query->where('t1.userid','!=',$userid);
                                                $query->where('t1.assistantcreated','=','1');
                                            }
        
                                        }
                                    })
                                ->where(function($query) use ($statetype){
                                if ($statetype!='') {
                                        if(in_array($statetype,array('0','1','10')))
                                        $query->where('t1.state','=', $statetype);
                                                    }	
                                })
                                ->where(function($query) use ($artistidsearch)
                                    {
                                            if (!empty($artistidsearch)) {
                                        $query->whereIn('t3.spid', $artistidsearch);
                                        
                                                }	
                                })
                                ->where(function($query) use ($managersearch,$isfulltextsearch)
                                    {
                                        if ($managersearch!='' && $isfulltextsearch!='1') {
                                            $query->where('t1.spid','=',$managersearch)
                                            ->orWhere('t1.displayname', 'LIKE', '%'.$managersearch.'%');
                                        }
                                    })
                                ->where(function($query) use ($managersearch,$isfulltextsearch,$fulltextsearchstring)
                                    {
                                        if ($managersearch!='' && $isfulltextsearch=='1') {
                                            $query->where('t1.spid','=',$managersearch)
                                            ->orWhereRaw("MATCH (displayname) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
                                        }
                                    })
                                ->count();
                                
       $gen_acc_countries=DB::table('spotify_statistics_gen_country AS t1')
                   ->select('t1.*')
                   ->orderByRaw('t1.country ASC')
                   ->get();


                   $s_c=0;
					foreach($gen_acc_countries as $gen_acc_countries_s)
								{
                                    

                     $countries[$s_c]=$gen_acc_countries_s;
									$s_c++;
                                }
                    


             $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
                   foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
                            {
                                      $$paginationarray_key=$paginationarray_value;
                            }
		
		$meta=array(

		'title' => 'Connected Spotify Accounts - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Connected Spotify Accounts - Admin Panel',

		'keywords' => '',

	);




		return view('admin/spotifyaccounts', [
            'countries'=>$countries,
            'notconnectedaccounts'=>$notconnectedaccounts,
            'allresults'=>$this->allresults,
            'meta' => $meta,
            'item_count'=> number_format($item_count),
            'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
            'pagination'=>$pagination,'paginationdisplay'=>$paginationdisplay,
		]);
		
		

    }
    

    public function returnEmpty(Request $request)
	{

		$meta=array(

			'title' => 'Connected Spotify Accounts - No Results - Admin Panel | '.config('myconfig.config.sitename_caps'),
	
			'description' => 'Connected Spotify Accounts - No Results - Admin Panel',
	
			'keywords' => '',
	
		);

		$paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,0,10);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}
		
		return view('admin/spotifytracks', [
		'meta' => $meta,
		'item_count'=> 0,
		'orderby'=>'','i2'=>0,'perpage'=>0,
		'pagination'=>'','paginationdisplay'=>$paginationdisplay,
		]);
		

	}

	
	
}
