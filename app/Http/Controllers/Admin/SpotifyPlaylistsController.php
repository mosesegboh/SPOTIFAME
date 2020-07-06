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


class SpotifyPlaylistsController extends Controller
{
    
    private $settings;

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


        $managersearch=urldecode($request->input('managersearch'));
            if($managersearch!='')
                {
                    $itemresponse=array();
                    $itemresponse=SpotifyHelper::instance()->getSpotifyUserItemId($managersearch);
                    if($itemresponse['id']!='')
                    $managersearch=$itemresponse['id'];
                    
                }
        

        $accounttype=$request->input('accounttype');

        if ($request->input('playlistid'))
        $playlistid=$request->input('playlistid');

        $isfulltextsearch=1;
        $fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($managersearch,1);

        $theresultset = 
                    DB::table('spotify_accounts_auth as t1')
                    ->select('t1.*','t2.username','t4.name AS firstplaylistname','t3.spid AS playlistid','t4.id AS mydbid','t3.managerid')
                    ->leftJoin('users AS t2', function($join)
							{
							$join->on('t1.userid', '=', 't2.id');
                            })
                    ->leftJoin('spotify_accounts_auth_realplaylists AS t3', function($join)
							{
                            $join->on('t1.id', '=', 't3.managerid');

                            if ($this->useridshouldbeused) {
                            $join->on('t2.id','=', 't3.userid');
                                }
							})
                    ->leftJoin('spotify_items AS t4', function($join)
							{
                            $join->on('t3.spid', '=', 't4.itemid');
                            $join->where('t4.type','=', 'playlist');
                            })
                    ->where(function($query) use ($playlistid)
                            {
                                if ($playlistid!='') {
                                    $query->where('t3.spid','=',$playlistid);
                                }
                            })
                    ->where(function($query) use ($userid)
                            {
                                if ($this->useridshouldbeused) {
                                    $query->where('t1.userid','=',$userid);
                                }
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
                    ->where(function($query) use ($managersearch,$isfulltextsearch)
                                 {
                                     if ($managersearch!='' && $isfulltextsearch!='1') {
                                         $query->where('t1.spid','=', $managersearch)
                                         ->orWhere('t1.displayname', 'LIKE', '%'.$managersearch.'%');
                                     }
                                 })
                    ->where(function($query) use ($managersearch,$isfulltextsearch,$fulltextsearchstring)
                                 {
                                     if ($managersearch!='' && $isfulltextsearch=='1') {
                                        $query->where('t1.spid','=', $managersearch)
                                        ->orWhereRaw("MATCH (displayname) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
                                     }
                                 })
                    ->whereNotNull('t4.name')
                    ->groupBy('t3.managerid')
					->orderByRaw('t1.id DESC')
					->offset($offset)
					->limit($this->settings['sp_perpage'])
                    ->get();

                    $item_count = 
                    DB::table('spotify_accounts_auth as t1')
                    ->select('t1.*','t2.username','t4.name AS firstplaylistname','t3.spid AS playlistid')
                    ->leftJoin('users AS t2', function($join)
							{
							$join->on('t1.userid', '=', 't2.id');
                            })
                    ->leftJoin('spotify_accounts_auth_realplaylists AS t3', function($join)
							{
                            $join->on('t1.id', '=', 't3.managerid');

                            if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
    
                                    if(Auth::user()->isAssistant())
                                    {
                                    }
                                    else
                                $join->on('t2.id','=', 't3.userid');
                            }
                                    
							})    
                    ->leftJoin('spotify_items AS t4', function($join)
							{
                            $join->on('t3.spid', '=', 't4.itemid');
                            $join->where('t4.type','=', 'playlist');
                            })

                    ->where(function($query) use ($playlistid)
                            {
                                if ($playlistid!='') {
                                    $query->where('t3.spid','=',$playlistid);
                                }
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
                   ->where(function($query) use ($managersearch,$isfulltextsearch)
                                 {
                                     if ($managersearch!='' && $isfulltextsearch!='1') {
                                         $query->where('t1.spid','=', $managersearch)
                                         ->orWhere('t1.displayname', 'LIKE', '%'.$managersearch.'%');
                                     }
                                 })
                    ->where(function($query) use ($managersearch,$isfulltextsearch,$fulltextsearchstring)
                                 {
                                     if ($managersearch!='' && $isfulltextsearch=='1') {
                                        $query->where('t1.spid','=', $managersearch)
                                        ->orWhereRaw("MATCH (displayname) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
                                     }
                                 })
                    ->whereNotNull('t4.name')
                    ->groupBy('t3.managerid')
                    ->get()
                    ->count();
                    
                    
                    $s_c=0;
                    foreach($theresultset as $theresultset_s)
								{


                    $theresultset2 = 

                    DB::table('spotify_accounts_auth_realplaylists AS t3')
                    ->select('t4.*','t3.spid AS playlistid','t4.id AS mydbid')
                    ->leftJoin('spotify_items AS t4', function($join)
							{
                            $join->on('t3.spid', '=', 't4.itemid');
                            $join->where('t4.type','=', 'playlist');
                            })
                    ->where('t3.managerid', '=', $theresultset_s->managerid)
                    ->where('t3.userid', '=', $theresultset_s->userid)
                    ->whereNotNull('t4.name')
					->orderByRaw('t3.id DESC')
                    ->get();



                    $theresultset_s->playlist_count=0;
                    $theresultset_s->playlist_count=count($theresultset2);
                    $theresultset_s->playlists=$theresultset2;

                    $theresultset[$s_c]=$theresultset_s;
                    $s_c++;
                        }

                       
				

                    $this->allresults=$theresultset;


             $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
            
		
                   foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
                            {
                                      $$paginationarray_key=$paginationarray_value;
                            }
        
                            

		$meta=array(

		'title' => 'Connected Spotify Playlists - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Connected Spotify Playlists - Admin Panel',

		'keywords' => '',

	);




		return view('admin/spotifyplaylists', [
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

            'title' => 'Connected Spotify Playlists - Admin Panel | '.config('myconfig.config.sitename_caps'),
    
            'description' => 'Connected Spotify Playlists - Admin Panel',
    
            'keywords' => '',
    
        );


        $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,0,10);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
        }
        

        return view('admin/spotifyplaylists', [
            'allresults'=>'',
            'meta' => $meta,
            'item_count'=> 0,
            'orderby'=>'','i2'=>0,'perpage'=>0,
            'pagination'=>$pagination,'paginationdisplay'=>$paginationdisplay,
		]);
    }

	
	
}
