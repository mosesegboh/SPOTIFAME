<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;
use Carbon\Carbon;

class SpotifyTracksController extends Controller
{
    private $settings;

    private $orderby;

    private $tracks=array();

    public function __construct()
    {

		$this->middleware('auth');


        $this->generateConfig();

	$this->settings=$this->getSettings();

	

	}

    public function getPage(Request $request)
    {


		$spotifyapi=SpotifyHelper::instance()->getSpotifySearchTokens();


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
        
        $namesearch='';
        $namesearch=urldecode($request->input('namesearch'));
            if($namesearch!='')
            {
                $itemresponse=array();
                $itemresponse=SpotifyHelper::instance()->getSpotifyTrackItemId($namesearch);
                if($itemresponse['id']!='')
                $namesearch=$itemresponse['id'];
                
                        
            }

        $playlistsearch=urldecode($request->input('playlistsearch'));
        if($playlistsearch!='')
            {
                $itemresponse=array();
                $itemresponse=SpotifyHelper::instance()->getSpotifyPlaylistItemId($playlistsearch);
                if($itemresponse['id']!='')
                $playlistsearch=$itemresponse['id'];
                
            }
            

        $albumsearch=urldecode($request->input('albumsearch'));
        if($albumsearch!='')
            {
                $itemresponse=array();
                $itemresponse=SpotifyHelper::instance()->getSpotifyAlbumItemId($albumsearch);
                if($itemresponse['id']!='')
                $albumsearch=$itemresponse['id'];
                
            }

        $artistsearch=urldecode($request->input('artistsearch'));
        if($artistsearch!='')
            {
                $itemresponse=array();
                $itemresponse=SpotifyHelper::instance()->getSpotifyArtistItemId($artistsearch);
                if($itemresponse['id']!='')
                $artistsearch=$itemresponse['id'];
                
            }


        $albumidsearch=[];
        if($albumsearch!='')
        {

            $isfulltextsearch=1;
            $fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($albumsearch,1);

            $get_results = 
                    DB::table('spotify_accounts_auth_realalbums AS t1')
                    ->select('t1.*')
                    ->where(function($query) use ($albumsearch,$isfulltextsearch)
                            {
                                if ($albumsearch!='' && $isfulltextsearch!='1') {
                                    $query->where('t1.spid','=', $albumsearch)
                                    ->orWhere('t1.name', 'LIKE', '%'.$albumsearch.'%');
                                }
                            })
                        ->where(function($query) use ($albumsearch,$isfulltextsearch,$fulltextsearchstring)
                            {
                                if ($albumsearch!='' && $isfulltextsearch=='1') {
                                    $query->where('t1.spid','=', $albumsearch)
                                    ->orWhereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
                                }
                            })
                    ->get();

                    foreach($get_results as $get_results_s)
								{
                 $albumidsearch[]= $get_results_s->spid;
                                }

                  if(empty($albumidsearch))
                return $this->returnEmpty($request);

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
                                    $query->where('t1.itemid','=', $artistsearch)
                                    ->orWhere('t1.name', 'LIKE', '%'.$artistsearch.'%');
                                }
                            })
            ->where(function($query) use ($artistsearch,$isfulltextsearch,$fulltextsearchstring)
                            {
                                if ($artistsearch!='' && $isfulltextsearch=='1') {
                                    $query->where('t1.itemid','=', $artistsearch)
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


        $trackids=array();
        if($playlistsearch!='')
        {

            $isfulltextsearch=1;
            $fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($playlistsearch,1);
            
            $get_results = 
            DB::table('spotify_trackplaylist_fk AS t10')
            ->select('t10.track_id')
            ->leftJoin('spotify_accounts_auth_realplaylists AS t2', function($join)
							{
							$join->on('t10.playlist_id', '=', 't2.id');
                            })
           ->leftJoin('spotify_items AS t1', function($join)
							{
							$join->on('t1.itemid', '=', 't2.spid');
                            })
            ->where(function($query) use ($playlistsearch,$isfulltextsearch)
                            {
                                if ($playlistsearch!='' && $isfulltextsearch!='1') {
                                    $query->where('t1.itemid','=', $playlistsearch)
                                    ->orWhere('t1.name', 'LIKE', '%'.$playlistsearch.'%');
                                }
                            })
            ->where(function($query) use ($playlistsearch,$isfulltextsearch,$fulltextsearchstring)
                            {
                                if ($playlistsearch!='' && $isfulltextsearch=='1') {
                                    $query->where('t1.itemid','=', $playlistsearch)
                                    ->orWhereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
                                }
                            })
            ->where('t1.type', '=', 'playlist')
            ->get();

            foreach($get_results as $get_results_s)
                        {
         $trackids[]= $get_results_s->track_id;
                        }


         $trackids=array_unique(array_filter($trackids));

                        if(empty($trackids))
                        return $this->returnEmpty($request);

              
                       
                
            
        }




        $this->orderby='t1.timestamp DESC'; //default
		
		
        $isfulltextsearch=1;
        $fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($namesearch,1);

        $theresultset = 
					DB::table('spotify_accounts_auth_realtracks as t1')
                    ->select('t1.*','t3.popularity','t3.id AS realitemid','t3.type')
                    ->leftJoin('spotify_items AS t3', function($join)
							{
							$join->on('t3.itemid', '=', 't1.spid');
                            })
                    ->where(function($query) use ($namesearch,$isfulltextsearch)
                           {
                               if ($namesearch!='' && $isfulltextsearch!='1') {
                                   $query->where('t1.spid','=', $namesearch)
                                   ->orWhere('t1.name', 'LIKE', '%'.$namesearch.'%');
                               }
                           })
                    ->where(function($query) use ($namesearch,$isfulltextsearch,$fulltextsearchstring)
                           {
                               if ($namesearch!='' && $isfulltextsearch=='1') {
                                   $query->where('t1.spid','=', $namesearch)
                                   ->orWhereRaw("MATCH (t1.name) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
                               }
                           })
                    ->where(function($query) use ($albumidsearch)
                           {
                            if (!empty($albumidsearch)) {
                                  $query->whereIn('t1.albumid', $albumidsearch);
                                }	
                          })
                    ->where(function($query) use ($artistidsearch)
                          {
                                if (!empty($artistidsearch)) {
                                 $query->whereIn('t1.artistid', $artistidsearch);
                                         }	
                         })
                     ->where(function($query) use ($trackids)
                         {
                             if (!empty($trackids)) {
                                 $query->whereIn('t1.id',$trackids);
                             }
                         })
					->orderByRaw($this->orderby)
					->offset($offset)
					->limit($this->settings['sp_perpage'])
					->get();
                    
                    
                    $s_c=0;
					foreach($theresultset as $theresultset_s)
								{


                                    $theresultset2 = 
                    DB::table('spotify_accounts_auth_realplaylists AS t3')
                    ->select('t4.*','t3.spid',)
                    ->leftJoin('spotify_items AS t4', function($join)
							{
                            $join->on('t3.spid', '=', 't4.itemid');
                            $join->where('t4.type','=', 'playlist');
                            })
                    ->leftJoin('spotify_trackplaylist_fk AS t5', function($join)
							{
                            $join->on('t5.playlist_id', '=', 't3.id');
                            })
                    ->where('t5.track_id','=',$theresultset_s->id)
                    ->whereNotNull('t4.name')
					->orderByRaw('t3.id DESC')
                    ->get();
                    

                    $theresultset_s->playlists=$theresultset2;


									$theresultset[$s_c]=$theresultset_s;
									$s_c++;
								}

	$this->tracks=$theresultset;

								
								$item_count = 
                                DB::table('spotify_accounts_auth_realtracks as t1')
                                ->where(function($query) use ($namesearch)
                                            {
                                    if ($namesearch!='') {
                                                $query->where('t1.name','=',$namesearch)
                                                ->orWhere('t1.spid', '=', $namesearch);
                                                        }	
                                        })
                                    ->where(function($query) use ($albumidsearch)
                                        {
                                         if (!empty($albumidsearch)) {
                                               $query->whereIn('t1.albumid', $albumidsearch);
                                             }	
                                       })
                                    ->where(function($query) use ($artistidsearch)
                                        {
                                              if (!empty($artistidsearch)) {
                                               $query->whereIn('t1.artistid', $artistidsearch);
                                                       }	
                                       })
                                   ->where(function($query) use ($trackids)
                                        {
                                            if (!empty($trackids)) {
                                                $query->whereIn('t1.id',$trackids);
                                            }
                                        })
									->count();


            

                                    
		
		$meta=array(

		'title' => 'Tracks In Our Playlists - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Tracks In Our Playlists - Admin Panel',

		'keywords' => '',

	);

        $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}

		

		return view('admin/spotifytracks', [
		'tracks'=>$this->tracks,
		'meta' => $meta,
		'item_count'=> number_format($item_count),
		'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
		'pagination'=>$pagination,
        'paginationdisplay'=>$paginationdisplay,
		]);



    }
    

    public function returnEmpty(Request $request)
	{

		$meta=array(

			'title' => 'Tracks In Our Playlists - No Results - Admin Panel | '.config('myconfig.config.sitename_caps'),
	
			'description' => 'Tracks In Our Playlists - No Results - Admin Panel',
	
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
