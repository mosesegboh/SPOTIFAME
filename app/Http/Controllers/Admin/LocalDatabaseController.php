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


class LocalDatabaseController extends Controller
{
    
	private $settings;

	private $orderby;

	private $searchtypes=array();

	private $searchresults=array();

	private $yearfromstartyear;

	private $yeartostartyear;

	private $rangeslidervalues=array();

	private $fromfollowers;

	private $tofollowers;

	private $hidespotifyowned;

	private $claimedshow;

	private $claimedshow2;
	
	private $notclaimedshow;
	
	private $unknownshow;
	
	private $claimprogress='';

	private $item_left=0;

	private $item_queue_count=0;

    public function __construct()
    {

		$this->middleware('auth');

		$this->searchtypes=array(
			'artist',
			'playlist',
			);

	$this->generateConfig();

	$this->settings=$this->getSettings();

	$this->rangeslidervalues=array(
		$this->settings['sp_range_min'],
		$this->settings['sp_range_max'],
		$this->settings['sp_range_break']

	);


	}

	public function downloadTheResultset(Request $request)
    {
	
		if(!$request->input('file') || $request->input('file')=='')
		return redirect('notfound');

		function readfile_chunked($filename,$retbytes=true) {
			$chunksize = 1*(1024*1024); // how many bytes per chunk
			$buffer = '';
			$cnt =0;
			// $handle = fopen($filename, 'rb');
			$handle = fopen($filename, 'rb');
			if ($handle === false) {
				return false;
			}
			while (!feof($handle)) {
				$buffer = fread($handle, $chunksize);
				echo $buffer;
				ob_flush();
				flush();
				if ($retbytes) {
					$cnt += strlen($buffer);
				}
			}
				$status = fclose($handle);
			if ($retbytes && $status) {
				return $cnt; // return num. bytes delivered like readfile() does.
			}
			return $status;
		 
		 } 



		 $filename =$request->input('file');

		$filelocation="../storage/downloads/excelresultset/".$request->input('file');


			header('Content-Description: File Transfer');
			//header('Content-Type: application/octet-stream');
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=' . $filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . ( filesize($filelocation) ) );
			readfile_chunked($filelocation);
			@unlink($filelocation);



	

	}
	
    public function getPage(Request $request)
    {


$ipAddress = $_SERVER['REMOTE_ADDR'];


		$userid = Auth::id();
		
		$isfulltextsearch=1;
		if ($request->input('isfulltextsearch')=='1')
		{
			$isfulltextsearch=1;
		}

		if(!in_array($request->input('searchtype'),$this->searchtypes) && $request->input('searchtype')) 
			{
			return $this->returnEmpty($request);
			}

		if ($request->input('title'))
		{
			$searchname=urldecode($request->input('title'));
			$fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($searchname,1);
			
		}
		

		if ($request->input('pagenum'))
		$offset = ($request->input('pagenum')-1)*$this->settings['sp_perpage'];
		else
		$offset = '0';

		
		$this->hidespotifyowned=$request->input('hidespotifyowned');

		$this->artistswithoutgenres=$request->input('artistswithoutgenres');

		if($request->input('searchtype') !='')
		$searchtype=$request->input('searchtype');
		else
		$searchtype='artist';

		if($request->input('orderby') =='' || !$request->input('orderby'))
		$orderbyget='';
		else
		$orderbyget=$request->input('orderby');

		if($orderbyget=='added')
		$this->orderby='t1.timestamp DESC';
		elseif($orderbyget=='followers')
		$this->orderby='t1.followercount DESC';
		elseif($orderbyget=='name')
		$this->orderby='t1.name ASC';
		else
		$this->orderby='t1.id ASC'; //default


		if($searchtype=='artist')
		{
			
			if($request->input('claimedshow')=='on' || !$request->input('searchset'))
			$this->claimedshow='on';
			if($request->input('claimedshow2')=='on' || !$request->input('searchset'))
			$this->claimedshow2='on';
			if($request->input('notclaimedshow')=='on' || !$request->input('searchset'))
			$this->notclaimedshow='on';
			if($request->input('unknownshow')=='on' || !$request->input('searchset'))
			$this->unknownshow='on';

			if($this->claimedshow!='on' && $this->claimedshow2!='on' && $this->notclaimedshow!='on' && $this->unknownshow!='on')
			{
				return $this->returnEmpty($request);
			}
		
		}

		


		if($this->artistswithoutgenres=='on')
		$request->input('genres')=='';

		if($searchtype=='artist')
		{
				$searchgenrestring='';
				$searchgenre=array();
				$genre_ids=array();
				if($request->input('genres') !='')
				{
					//$thegenrearray=sort(explode(',',urldecode($request->input('genres'))));
					$thegenrearray=explode(',',urldecode($request->input('genres')));
					sort(array_filter($thegenrearray));
					
					
					foreach ($thegenrearray as $thegenrearray_s)
					{

						$thegenrearray_s=trim($thegenrearray_s);
				$searchgenre[]=$thegenrearray_s;

				

				$getgenre = 
				DB::table('spotify_genres as t1')
				->select('t1.*','t2.item_id')
				->leftJoin('spotify_itemgenre_fk AS t2', function($join)
							{
							$join->on('t2.genre_id', '=', 't1.id');
							})
				->where(function($query) use ($thegenrearray_s)
							{
								if (preg_match('#^(\'|").+\1$#', $thegenrearray_s) != 1)
								{
									$query->where('t1.name','LIKE',$thegenrearray_s.'%');
								}
								else
								{
									$query->where('t1.name','=',trim($thegenrearray_s, '"'));

								}
							})
				->get();


						foreach ($getgenre as $getgenre_s)
						{
							$genre_ids[]=$getgenre_s->item_id;
						}
							


					}


					$genre_ids=array_unique(array_filter($genre_ids));

					if (empty($genre_ids))
					return $this->returnEmpty($request);

					$searchgenrestring.=join(',',$thegenrearray);

				}

				

		}

		
		if($ipAddress=='88.91.243.154')
						{
							//print_r($searchgenre);
						}


		$inputfolmin_cache='';
		$inputfolmax_cache='';
		if($searchtype=='artist' || $searchtype=='playlist')
		{

			if(urldecode($request->input('followers'))!='')
			$followerstringinput=urldecode($request->input('followers'));
			else
			$followerstringinput=$this->rangeslidervalues[0].';'.$this->rangeslidervalues[1];

			$followerstring=explode(';',$followerstringinput);
			$inputfolmin=SpotifyHelper::instance()->transformRangeValue($followerstring[0],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
			$inputfolmax=SpotifyHelper::instance()->transformRangeValue($followerstring[1],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
				
				
			//$inputfolmin_cache='folmin:'.$inputfolmin;
			//$inputfolmax_cache='folmax:'.$inputfolmax;
		}
		
		

			
			$claimedstring1='0';
			if ($this->claimedshow=='on') {
				$claimedstring1='1';
			}
			$claimedstring2='0';
			if ($this->claimedshow2=='on') {
				$claimedstring2='1';
			}
			$claimedstring3='0';
			if ($this->notclaimedshow=='on') {
				$claimedstring3='1';
			}
			$claimedstring4='0';
			if ($this->unknownshow=='on') {
				$claimedstring4='1';
			}

			$artistswithoutgenresstring='0';
			if($this->artistswithoutgenres=='on') {
				$artistswithoutgenresstring='1';
			}

			$hidespotifyownedstring='0';
			if($this->hidespotifyowned=='on') {
				$hidespotifyownedstring='1';
			}
		

		$search_string=str_replace('_','',$searchtype).'_'.
		str_replace('_','',$searchname).'_'.
		str_replace('_','',$followerstring[0]).'_'.
		str_replace('_','',$followerstring[1]).'_'.
		str_replace('_','',$searchgenrestring).'_'.
		str_replace('_','',$artistswithoutgenresstring);
		/*
		str_replace('_','',$claimedstring1).'_'.
		str_replace('_','',$claimedstring2).'_'.
		str_replace('_','',$claimedstring3).'_'.
		str_replace('_','',$claimedstring4);
		*/

		$search_string_more=str_replace('_','',$searchtype).'_'.
		str_replace('_','',$searchname).'_'.
		str_replace('_','',$followerstring[0]).'_'.
		str_replace('_','',$followerstring[1]).'_'.
		str_replace('_','',$searchgenrestring).'_'.
		str_replace('_','',$claimedstring1).'_'.
		str_replace('_','',$claimedstring2).'_'.
		str_replace('_','',$claimedstring3).'_'.
		str_replace('_','',$claimedstring4).'_'.
		str_replace('_','',$orderbyget).'_'.
		str_replace('_','',$artistswithoutgenresstring).'_'.
		str_replace('_','',$hidespotifyownedstring);



		if($searchtype=='artist')
		{
		$row_artists_claim_get = DB::table('spotify_artists_claim_queue')
					->where('searchstring', '=', base64_encode($search_string))
					->first();

					$artists_claim_id=$row_artists_claim_get->id;

					if($artists_claim_id>0)
					{
						echo $this->claimprogress;
						$this->claimprogress=$row_artists_claim_get->inprogress;
						$this->item_left=$row_artists_claim_get->item_left;
						$this->item_queue_count=$row_artists_claim_get->item_count;
						$thecacheid=$row_artists_claim_get->id;
					}

		}


		$theresultset = 
					DB::table('spotify_items as t1')
					->select('t1.*')
					->where(function($query) use ($searchtype,$genre_ids)
							{
								if ($searchtype=='artist' && !empty($genre_ids)) {
									$query->whereIn('t1.id',$genre_ids);
								}
							})
					->where(function($query) use ($searchname,$isfulltextsearch)
						{
							if ($searchname!='' && $isfulltextsearch!='1') {
								$query->where('t1.name','LIKE', '%'.$searchname.'%');
							}
						})
					->where(function($query) use ($searchname,$isfulltextsearch,$fulltextsearchstring)
						{
							if ($searchname!='' && $isfulltextsearch=='1') {
								$query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
							}
						})
					->where('t1.type','=',$searchtype)
					->where('t1.followercount', '>=', $inputfolmin)
					->where('t1.followercount', '<=', $inputfolmax)
					->where(function($query) use ($searchtype)
						{
							if ($searchtype=='playlist' && $this->hidespotifyowned=='on') {
								$query->where('t1.ownername','!=','Spotify');
							}
						})
					->where(function($query)
						{
							if ($this->artistswithoutgenres=='on') {
								$query->where(function ($query) {
									$query->where('t1.genres', '=', '')
										->orWhereNull('t1.genres');
								});
							}
						})
					->where(function($query)
						{
							if ($this->claimedshow=='on') {
								$query->orWhere('t1.claimed','=',1);
							}
							if ($this->claimedshow2=='on') {
								$query->orWhere('t1.claimed','=',3);
							}
							if ($this->notclaimedshow=='on') {
								$query->orWhere('t1.claimed','=',2);
							}
							if ($this->unknownshow=='on') {
								$query->orWhere('t1.claimed','=',0);
							}
							
                        })
					->orderByRaw($this->orderby)
					->offset($offset)
					->limit($this->settings['sp_perpage'])
                    ->get();

					

// Your Eloquent query executed by using get()

//dd(DB::getQueryLog()); // Show results of log

					$s_c=0;
					foreach($theresultset as $theresultset_s)
								{

									$theresultset[$s_c]->followers=new \stdClass();
									$theresultset[$s_c]->followers->total=$theresultset_s->followercount;

									$theresultset[$s_c]->external_urls=new \stdClass();
									$theresultset[$s_c]->external_urls->spotify=$theresultset_s->url;

									$theresultset[$s_c]->images=array();
									$theresultset[$s_c]->images[2]=new \stdClass();
									$theresultset[$s_c]->images[2]->url=$theresultset_s->imageurl;

									$theresultset[$s_c]->mydbid=$theresultset[$s_c]->id;
									$theresultset[$s_c]->id=$theresultset[$s_c]->itemid;

									if($searchtype=='playlist')
									{
									$theresultset[$s_c]->owner=new \stdClass();
									$theresultset[$s_c]->owner->external_urls=new \stdClass();
									$theresultset[$s_c]->owner->external_urls->spotify=$theresultset[$s_c]->ownerurl;
									$theresultset[$s_c]->owner->display_name=$theresultset[$s_c]->ownername;
									}


									

									$s_c++;
								}

								$this->searchresults=$theresultset;
		
$donotget_itemcount='0';
	if($searchtype=='artist'
		&& ($request->input('genres')=='' || !$request->input('genres'))
		&& $this->claimedshow=='on' && $this->claimedshow2=='on'
		&& $this->notclaimedshow=='on' && $this->unknownshow=='on'
		&& $this->artistswithoutgenres!='on'
		&& $searchname==''
		&& $inputfolmin==$this->rangeslidervalues[0]
		&& $inputfolmax==$this->rangeslidervalues[1]
		)
	{
		$donotget_itemcount='1';	
		
		$stats= DB::table('spotify_statistics')
		->where('realname', '=', 'allartists')
		->get();
		foreach ($stats as $stats_s)
		{
			$item_count=$stats_s->realvalue;
		}


	}


	if($searchtype=='playlist'
		&& $this->hidespotifyowned!='on'
		&& $searchname==''
		&& $inputfolmin==$this->rangeslidervalues[0]
		&& $inputfolmax==$this->rangeslidervalues[1]
		)
	{
		$donotget_itemcount='1';	
		
		$stats= DB::table('spotify_statistics')
		->where('realname', '=', 'allplaylists')
		->get();
		foreach ($stats as $stats_s)
		{
			$item_count=$stats_s->realvalue;
		}


	}



			if($donotget_itemcount=='0')
			{
								$item_count = 
								DB::table('spotify_items as t1')
								
								->where(function($query) use ($searchtype,$genre_ids)
								{
									if ($searchtype=='artist' && !empty($genre_ids)) {
										$query->whereIn('t1.id',$genre_ids);
									}
								})
								->where(function($query) use ($searchname,$isfulltextsearch)
								{
									if ($searchname!='' && $isfulltextsearch!='1') {
										$query->where('t1.name','LIKE', '%'.$searchname.'%');
									}
								})
							->where(function($query) use ($searchname,$isfulltextsearch,$fulltextsearchstring)
								{
									if ($searchname!='' && $isfulltextsearch=='1') {
										$query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
									}
								})
								->where('t1.type','=',$searchtype)
								->where('t1.followercount', '>=', $inputfolmin)
								->where('t1.followercount', '<=', $inputfolmax)
								->where(function($query) use ($searchtype)
								{
									if ($searchtype=='playlist' && $this->hidespotifyowned=='on') {
										$query->where('t1.ownername','!=','Spotify');
									}
								})
								->where(function($query)
								{
									if ($this->artistswithoutgenres=='on') {
										$query->where(function ($query) {
											$query->where('t1.genres', '=', '')
												->orWhereNull('t1.genres');
										});
									}
								})
								->where(function($query)
								{
									if ($this->claimedshow=='on') {
										$query->orWhere('t1.claimed','=',1);
									}
									if ($this->claimedshow2=='on') {
										$query->orWhere('t1.claimed','=',3);
									}
									if ($this->notclaimedshow=='on') {
										$query->orWhere('t1.claimed','=',2);
									}
									if ($this->unknownshow=='on') {
										$query->orWhere('t1.claimed','=',0);
									}
									
								})
								->count();

			}


			if(empty($theresultset))
			return $this->returnEmpty($request);



		$paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		  {
			  $$paginationarray_key=$paginationarray_value;
		}

		
		$meta=array(

		'title' => 'Local Database - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Local Database - Admin Panel',

		'keywords' => '',

	);




		return view('admin/localdatabase', [
		'searchresults'=>$this->searchresults,
		'meta' => $meta,
		'item_count'=> number_format($item_count),
		'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
		'pagination'=>$pagination,'paginationdisplay'=>$paginationdisplay,
		'rangeslidervalues'=>$this->rangeslidervalues,
		'fromfollowers'=>$this->fromfollowers,
		'tofollowers'=>$this->tofollowers,
		'claimedshow'=>$this->claimedshow,
        'claimedshow2'=>$this->claimedshow2,
        'notclaimedshow'=>$this->notclaimedshow,
		'unknownshow'=>$this->unknownshow,
		'claimprogress'=>$this->claimprogress,
		'item_left'=>$this->item_left,
		'item_queue_count'=>$this->item_queue_count,
		'search_string'=>base64_encode($search_string),
		'search_string_more'=>base64_encode($search_string_more),
		'thecacheid'=>$thecacheid,
		]);
		
		

	}


	public function returnEmpty(Request $request)
	{

		$meta=array(

			'title' => 'Search:'.urldecode($request->input('title')).' - No Results - Admin Panel | '.config('myconfig.config.sitename_caps'),
	
			'description' => 'Search:'.urldecode($request->input('title')).' - No Results - Admin Panel',
	
			'keywords' => '',
	
		);

		$paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,0,10);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}
		
		return view('admin/localdatabase', [
		'meta' => $meta,
		'item_count'=> 0,
		'title'=>urldecode($request->input('title')),
		'orderby'=>'','i2'=>0,'perpage'=>0,
		'pagination'=>'','paginationdisplay'=>$paginationdisplay,
		'rangeslidervalues'=>$this->rangeslidervalues,
		'fromfollowers'=>$this->fromfollowers,
		'tofollowers'=>$this->tofollowers,
		'claimedshow'=>$this->claimedshow,
        'claimedshow2'=>$this->claimedshow2,
        'notclaimedshow'=>$this->notclaimedshow,
		'unknownshow'=>$this->unknownshow,
		'claimprogress'=>$this->claimprogress,
		'item_left'=>$this->item_left,
		'item_queue_count'=>$this->item_queue_count,
		'search_string'=>base64_encode($search_string),
		'search_string_more'=>base64_encode($search_string_more),
		'thecacheid'=>$thecacheid,
		]);
		

	}
	
	
	
}
