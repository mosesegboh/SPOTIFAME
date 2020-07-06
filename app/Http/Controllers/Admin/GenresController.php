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


class GenresController extends Controller
{
    
	private $settings;

	private $orderby;

	private $searchresults=array();

    public function __construct()
    {

		$this->middleware('auth');

		$this->searchtypes=array(
			'artist',
			'playlist',
			);

	$this->generateConfig();

    $this->settings=$this->getSettings();
    
    $this->settings['sp_perpage']=100;


	}

    public function getPage(Request $request)
    {


$ipAddress = $_SERVER['REMOTE_ADDR'];


		$userid = Auth::id();
		
		if ($request->input('title'))
		$searchname=urldecode($request->input('title'));

		if ($request->input('pagenum'))
		$offset = ($request->input('pagenum')-1)*$this->settings['sp_perpage'];
		else
		$offset = '0';

		if($request->input('orderby') =='' || !$request->input('orderby'))
		$orderbyget='';
		else
		$orderbyget=$request->input('orderby');

		if($orderbyget=='added')
        $this->orderby='t1.timestamp DESC';
        elseif($orderbyget=='name')
        $this->orderby='t1.name ASC';
		else
		$this->orderby='t1.item_count DESC'; //default



		$theresultset = 
					DB::table('spotify_genres as t1')
					->select('t1.*')
					->where(function($query) use ($searchname)
						{
							if ($searchname!='') {
								$query->where('t1.name','LIKE', '%'.$searchname.'%');
							}
						})
					->orderByRaw($this->orderby)
					->offset($offset)
					->limit($this->settings['sp_perpage'])
                    ->get();

					
                    $res_count=0;
					foreach($theresultset as $theresultset_s)
								{

									$genre_get = DB::table('spotify_accounts_auth_realplaylists AS t1')
                                        ->select('t3.displayname AS playlistownername','t3.email AS playlistowneremail','t2.name AS playlistname','t2.url AS playlisturl')
                                        ->leftJoin('spotify_items AS t2', function($join)
                                        {
                                        $join->on('t1.spid', '=', 't2.itemid');
										})
										->leftJoin('spotify_accounts_auth AS t3', function($join)
                                        {
                                        $join->on('t1.managerid', '=', 't3.id');
                                        })
                                        ->where('t1.id', '=', $theresultset_s->playlistid)
                                        ->limit(1)
										->get();
										
										foreach ($genre_get as $row) {
											
											$theresultset_s->playlistname = $row->playlistname;
											$theresultset_s->playlisturl = $row->playlisturl;
											$theresultset_s->playlistowneremail = $row->playlistowneremail;
											$theresultset_s->playlistownername = $row->playlistownername;
                                        }



                                    $theresultset[$res_count]=$theresultset_s;
                                
                                    $res_count++;
								}

				$this->allresults=$theresultset;


								$item_count = 
								DB::table('spotify_genres as t1')
								->where(function($query) use ($searchname)
									{
										if ($searchname!='') {
											$query->where('t1.name','LIKE', '%'.$searchname.'%');
										}
									})
								->count();


				$stats= DB::table('spotify_statistics')
					->where('realname', '=', 'nogenreartists')
					->get();
				foreach ($stats as $stats_s)
				{
				$nogenreartists=$stats_s->realvalue;
				}

			

		$paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		  {
			  $$paginationarray_key=$paginationarray_value;
		}

		
		$meta=array(

		'title' => 'Genres - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Genres - Admin Panel',

		'keywords' => '',

	);




		return view('admin/genres', [
		'nogenreartists'=>$nogenreartists,
		'allresults'=>$this->allresults,
		'meta' => $meta,
		'item_count'=> number_format($item_count),
		'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
		'pagination'=>$pagination,'paginationdisplay'=>$paginationdisplay,
		]);
		
		

	}


	
}
