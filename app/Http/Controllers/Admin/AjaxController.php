<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;


use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

use Illuminate\Support\Facades\Crypt;

class AjaxController extends Controller
{
	private $rangeslidervalues=array();

	public function __construct()
    {
        $this->middleware('auth');
		
		$this->generateConfig();

		$this->settings=$this->getSettings();


		$this->rangeslidervalues=array(
			$this->settings['sp_range_min'],
			$this->settings['sp_range_max'],
			$this->settings['sp_range_break']

		);

	}

	public function updateOwnField(Request $request) {
		
		$userid=Auth::id();

		
		$itemid = $request->input('itemid');

		$change = $request->input('change');
		
		$table = $request->input('table');

		$newvalue  = $request->input('sentvalue');
		$rowtochange  = $request->input('row');

		
		$allowedtables=array(
			'spotify_accounts_auth'=>array('update'),
			'spotify_items'=>array('update'),

		);

		$badupdate=1;
		foreach ($allowedtables as $allowedtables_s_key => $allowedtables_s_value)
		{
			if($table==$allowedtables_s_key)
			{
					if(in_array($change,$allowedtables_s_value))
					{
						$badupdate=0;
					}
			}
		}

		if($badupdate)
		{
			return response()->json(array('status'=>"failed",'msg'=>'Not found or not allowed'), 200);
		}
		
		
		if($change=='update')
		{

			if($table='spotify_items')
			{
			DB::table($table.' AS t1')
			->leftJoin('spotify_accounts_auth_realplaylists AS t2', function($join) use ($userid)
							{
							$join->on('t2.spid', '=', 't1.itemid');
							if (!Auth::user()->isAdmin() && !Auth::user()->isEditor() && !Auth::user()->isAssistant()) {
								$join->where('t2.userid', '=', $userid);
							}
							})
			->leftJoin('spotify_accounts_auth AS t3', function($join) use ($userid)
							{
							$join->on('t3.id', '=', 't2.managerid');
							if (!Auth::user()->isAdmin() && !Auth::user()->isEditor() && !Auth::user()->isAssistant()) {
								$join->where('t3.userid', '=', $userid);
							}
							})
			->where('t3.active', '1')
			->where('t1.id', $itemid)
			->where(function($query) use ($userid,$request) {
				if (!Auth::user()->isAdmin() && !Auth::user()->isEditor() && !Auth::user()->isAssistant()) {
					   $query->where('t2.userid', $userid);
						 }
				})
			->where(function($query) use ($userid,$request) {
				if (!Auth::user()->isAdmin() && !Auth::user()->isEditor() && !Auth::user()->isAssistant()) {
					   $query->where('t3.userid', $userid);
						 }
				})
			->where(function($query) {
					if (Auth::user()->isAssistant()) {
						   $query->where('t3.assistantcreated', '1');
							 }
				})
			->whereNotNull('t2.id')
			->update([$rowtochange => $newvalue]);
			}
			else
			{
				DB::table($table)
				->where('id', $itemid)
				->where('userid', $userid)
				->update([$rowtochange => $newvalue]);
			}
			
		}



		return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);
		
		
	}
	
	public function simpleUpdateField(Request $request) {
		
		$userid=Auth::id();
		
		$itemid = $request->input('itemid');

		$change = $request->input('change');
		
		$table = $request->input('table');

		$newvalue  = $request->input('sentvalue');
		$rowtochange  = $request->input('row');

	if(Auth::user()->isAdmin() || Auth::user()->isEditor())
	{
		$allowedtables=array(
			'spotify_groups'=>array('update'),
			'spotify_accounts_auth_realartists'=>array('update'),
			'spotify_cron_setter'=>array('update'),
			'spotify_search_cache'=>array('update'),
			'spotify_artists_claim_queue'=>array('update'),
			'spotify_items'=>array('update'),
			'users'=>array('update'),

		);
	}
	elseif(Auth::user()->isAssistant())
	{

		$allowedtables=array(
			'spotify_search_cache'=>array('update'),
			'spotify_items'=>array('update'),

		);

	}

		$badupdate=1;
		foreach ($allowedtables as $allowedtables_s_key => $allowedtables_s_value)
		{
			if($table==$allowedtables_s_key)
			{
					if(in_array($change,$allowedtables_s_value))
					{
						$badupdate=0;
					}
			}
		}

		if($badupdate)
		{
			return response()->json(array('status'=>"failed",'msg'=>'Not found or not allowed'), 200);
		}
		
		
		if($change=='update')
		{
			DB::table($table)
            ->where('id', $itemid)
            ->update([$rowtochange => $newvalue]);
			
		}



		return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);
		
		
	}

	public function showPassword(Request $request) {

		$userid=Auth::id();


		$itemid=$request->input('itemid');

		$row_get = DB::table('spotify_accounts_auth')
		->where(function($query) use ($userid,$request) {
			if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
				   $query->where('userid','=',$userid);
					 }
			})
		->where('active', '=', '1')
		->where('id', '=', $itemid)
		->limit(1)
		->get();

		foreach ($row_get as $row) {
			$account[]=$row;
		}

		if(empty($account))
				{
 return response()->json(array('status'=>"failed",'msg'=>'You are not the owner of this account!'), 200);
				}

	return response()->json(array('status'=>"success",'passwd'=>Crypt::decryptString($account[0]->thingstr)), 200);
			


	}


	public function addStraightAccount(Request $request) {

		$userid=Auth::id();

		$manageremail = $request->input('manageremail');
		$addpassinput = $request->input('addpassinput');

		if($addpassinput=='')
		{
			return response()->json(array('status'=>"failed",'msg'=>'Password can not be empty.'), 200);
		}

		if($manageremail=='')
		{
			return response()->json(array('status'=>"failed",'msg'=>'Email can not be empty.'), 200);
		}

		if (!filter_var($manageremail, FILTER_VALIDATE_EMAIL)) {
			return response()->json(array('status'=>"failed",'msg'=>'Provide a valid email address.'), 200);
		}


		$row_get = DB::table('spotify_accounts_auth')
		->where('email', '=', $manageremail)
		->get();

		foreach ($row_get as $row) {
			$accounts[]=$row->id;
		}

		if(!empty($accounts))
				{
 return response()->json(array('status'=>"failed",'msg'=>'The account is already added.'), 200);
				}


		DB::table('spotify_accounts_auth')->insert(
			['email' => $manageremail, 
			'thingstr' => Crypt::encryptString($addpassinput), 
			'active' => '1',
			'state' => '0',
			'userid' => $userid,
			'dt' => Carbon::now(),
			'timestamp' => Carbon::now()->timestamp
			]
		);


		return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);
	}

	public function addPlaylists(Request $request) {

		$userid=Auth::id();
		
		$managerid = $request->input('managerid'); //if no manager id, loop through all user's managers!

		if(Auth::user()->isAdmin() && $request->input('userid'))
		$userid=$request->input('userid');

		$row_get = DB::table('spotify_accounts_auth')
		->where('state', '=', '1')
		->where('active', '=', '1')
		->where('userid', '=', $userid)
		->where('accesstoken', '!=', '')
		->where('refreshtoken', '!=', '')
		->orderByRaw('id ASC')
		->get();

		foreach ($row_get as $row) {
			$allmanagerids[]=$row->id;
		}


		$managerids=array();
			if($managerid!='')
			{
				if(empty($allmanagerids) || !in_array($managerid,$allmanagerids))
				{
 return response()->json(array('status'=>"failed",'msg'=>'You are not the owner of this account!'), 200);
				}
			$managerids[]=$managerid;
			}
		else
			{
			
			$managerids=$allmanagerids;

			}
			
			if(empty($managerids))
			{
	return response()->json(array('status'=>"failed",'msg'=>'No spotify account found, please add your spotify account to our website.'), 200);
			}

		$perpage=10;

		$refreshall = 0;
		if($request->input('refreshall')=='on')
		$refreshall = 1;

		$playlistfound=0;
		$myspotifyapi=new \stdClass();
		foreach($managerids as $managerids_s)
		{

			
			$myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($managerids_s);

			if($myspotifyapi=='wrong')
			continue;

			$options=new \stdClass();
			$options->limit=$perpage;
			$options->offset=0;

			//firstget
			$myplaylists=new \stdClass();
			$try=true;
			while($try)
			{
				try {
					
						$myplaylists = $myspotifyapi->getMyPlaylists($options);

					$try=false;
				break;
				} catch (\Exception $e) {

						if ($e->getCode() == 429) {

							$responseobject = $myspotifyapi->getRequest()->getLastResponse();
							$responsestatus=$responseobject['status'];
							$retryAfter = $responseobject['headers']['Retry-After'];

							
							sleep($retryAfter);
							
						}
						else
						{
							return response()->json(array('status'=>"failed",'msg'=>'Error Occured: '.$responsestatus), 200);
							$try=false;
						}
						
				}
			}
			//firstget

			$item_count=$myplaylists->total;
		
			if($item_count==0)
			{
			continue;
			}
			else
			{
			$playlistfound=1;
			}
			


				$curoffset=(int) 0;
				$playlistids=array();
			$try=true;
				$pagenotfound=0;

				$myrealplaylists=new \stdClass();
				while($curoffset<=$item_count && $try)
				{

							$options=new \stdClass();
							$options->limit=$perpage;
							$options->offset=$curoffset;

					$myrealplaylists=array();
					$try2=true;
					while($try2)
					{
						try {

							
							$myrealplaylists = $myspotifyapi->getMyPlaylists($options)->items;
							$try2=false;
						} catch (\Exception $e) {


							if ($e->getCode() == 429) {

								$responseobject = $myspotifyapi->getRequest()->getLastResponse();
								$responsestatus=$responseobject['status'];
								$retryAfter = $responseobject['headers']['Retry-After'];
								
								sleep($retryAfter);
								
							}
							else
							{
								
									return response()->json(array('status'=>"failed",'msg'=>'Error Occured: '.$responsestatus), 200);
								$try2=false;
								
							}

						}
					}

					$scount=0;
					foreach($myrealplaylists as $myrealplaylists_s)
						{

							$myrealplaylists[$scount]->followers=new \stdClass();
							$myrealplaylists[$scount]->followers->total=0;
							$myrealplaylists[$scount]->followers->total=SpotifyHelper::instance()->getSingleFollowerCount($myspotifyapi,'playlist',$myrealplaylists_s->id);


							$scount++;
						}


				$curoffset+=$perpage;

					if(empty($myrealplaylists))
					$try=false;
				}


				foreach($myrealplaylists as $myrealplaylists_s)
				{

					$imageurl='';
				if($myrealplaylists_s->images[2]->url)
				$imageurl=$myrealplaylists_s->images[2]->url;
				elseif($myrealplaylists_s->images[0]->url)
				$imageurl=$myrealplaylists_s->images[0]->url;

					
				$notfoundelement=1;


				$checkifexists_get = DB::table('spotify_items')
					->where('type', '=', 'playlist')
					->where('itemid', '=', $myrealplaylists_s->id)
					->limit(1)
					->get();

					
					foreach ($checkifexists_get as $row) {
						$checkifexists_results[] = $row;
					}

					if(!is_null($checkifexists_results) && !empty($checkifexists_results))
					{
						$notfoundelement=0;
					}


				if($refreshall || $notfoundelement)
					{
						
					DB::table('spotify_items')
					->updateOrInsert(
				['type' => 'playlist',
				 'itemid' => $myrealplaylists_s->id],
				[
					'name' => mb_substr($myrealplaylists_s->name,0, 500,'UTF-8'),
					'followercount' => $myrealplaylists_s->followers->total,
					'imageurl' => $imageurl,
					'url' => $myrealplaylists_s->external_urls->spotify,
					'ownerurl' => $myrealplaylists_s->owner->external_urls->spotify,
					'ownername' => mb_substr($myrealplaylists_s->owner->display_name,0, 500,'UTF-8'),
					'description' => $myrealplaylists_s->description,
					'collaborative' => $myrealplaylists_s->collaborative,
				'dt' => Carbon::now()]
						);
						

						$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_items')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}


						DB::table('spotify_accounts_auth_realplaylists')
					->updateOrInsert(
				[ 'spid' => $myrealplaylists_s->id],
				[
				'userid'=>$userid,
				'managerid'=>$managerids_s]
						);


						$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_accounts_auth_realplaylists')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}


					}
					else
					{

						
				
						DB::table('spotify_accounts_auth_realplaylists')
					->updateOrInsert(
				[ 'spid' => $myrealplaylists_s->id],
				[
				'dt' => Carbon::now(),
				'userid'=>$userid,
				'managerid'=>$managerids_s]
						);

						$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_accounts_auth_realplaylists')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}

					}


						




						
					
				}


				$playlistcount=0;

				$playlistcount = DB::table('spotify_accounts_auth_realplaylists as t1')
									->where('t1.managerid', '=', $managerids_s)
									->count();

				// update user playlist count
				DB::table('spotify_accounts_auth')
				->where('id', '=', $managerids_s)
				->update([
					'playlistcount'=>$playlistcount,
					]);
				// update user playlist count
				
		}

		

		if($playlistfound==0)
		return response()->json(array('status'=>"failed",'msg'=>'Sorry you do not have any playlists!'), 200);


		

		return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);
	}

	public function changeThing(Request $request) {

		$userid=Auth::id();
		
		$thingstr = $request->input('thingstr');
		$itemid = $request->input('itemid');


		$row_get = DB::table('spotify_accounts_auth')
		->where(function($query) use ($userid,$request) {
         if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
                $query->where('userid','=',$userid);
                  }
		 })
		->where('active', '=', '1')
		->where('id', '=', $itemid)
		->get();


		foreach ($row_get as $row) {
			$row_account[]=$row;
		}
		

		if($thingstr=='')
		{
			return response()->json(array('status'=>"failed",'msg'=>'Type in something!'), 200);
		}

		if($row_account[0]->id=='' || !$row_account[0]->id>0)
		{
			return response()->json(array('status'=>"failed",'msg'=>'Account is not owned by you!'), 200);
		}
	
	
		// update password
		DB::table('spotify_accounts_auth')
		->where('id', '=', $itemid)
		->update([
			'thingstr'=>Crypt::encryptString($thingstr),
			]);
		// update password

		if(Crypt::encryptString($thingstr)!=$row_account[0]->thingstr)
		{
		//if it is changed
		DB::table('spotify_accounts_auth')
		->where('id', '=', $itemid)
		->update([
			'state'=>'0',
			'accesstoken'=>'',
			'refreshtoken'=>'',
			'cookies'=>'',
			'bearertoken'=>'',
			]);
		//if it is changed
		}

		//added first time, so updating
		if($row_account[0]->thingstr=='')
		{
			/*
			DB::table('spotify_accounts_auth_realartists')
		->where('managerid', '=', $itemid)
		->where('id', '=', $row_account[0]->artistconnectid)
		->update([
			'artistpickstate'=>'2'
			]);
			*/


		}
		//added first time, so updating


	return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);

	}

	public function changeArtistPick(Request $request) {

		$userid=Auth::id();
		
		$artistpick = $request->input('artistpick');
		$itemid = $request->input('itemid');

		$requestchange = $request->input('requestchange'); //0=done 1=waiting 2=getfromspotify 10=problem

		$row_get = DB::table('spotify_accounts_auth')
		->where(function($query) use ($userid,$request) {
			if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
				   $query->where('userid','=',$userid);
					 }
			})
		->where('active', '=', '1')
		->where('id', '=', $itemid)
		->get();


		foreach ($row_get as $row) {
			$row_account[]=$row;
		}
		
		if($row_account[0]->id=='' || !$row_account[0]->id>0 || !in_array($requestchange,array('1','0')))
		{
			return response()->json(array('status'=>"failed",'msg'=>'Account is not owned by you, so you can\'t change artist\'s pick!'), 200);
		}

		if($row_account[0]->state!='1')
		{
			return response()->json(array('status'=>"failed",'msg'=>'Account is not yet connected to our system.'), 200);
		}

		if($row_account[0]->active!='1')
		{
			return response()->json(array('status'=>"failed",'msg'=>'Account is deactivated, you can\'t add artist\'s pick.'), 200);
		}


		if($row_account[0]->thingstr=='')
		{
			return response()->json(array('status'=>"failed",'msg'=>'You need to add spotify password first to change artist\'s pick!'), 200);
		}

		if($row_account[0]->artistconnectid=='')
		{
			return response()->json(array('status'=>"failed",'msg'=>'You need to connect an artist first!'), 200);
		}


		
	
		$row_get2 = DB::table('spotify_accounts_auth_realartists')
		->where('managerid', '=', $itemid)
		->where('id', '=', $row_account[0]->artistconnectid)
		->get();

		foreach ($row_get2 as $row2) {
			$row_account2[]=$row2;
		}
		
		if($artistpick=='')
		{


			DB::table('spotify_accounts_auth_realartists')
		->where('managerid', '=', $itemid)
		->where('id', '=', $row_account2[0]->id)
		->update([
			'artistpick'=>'',
			'artistpickstate'=>$requestchange
			]);


			//delete and message success!
			return response()->json(array('status'=>"success",'msg'=>'Success, removed!'), 200);
		}


		$realidtocheck=$artistpick;
		$checkitem=SpotifyHelper::instance()->getSpotifyItemId($realidtocheck);

		
			if(!in_array($checkitem['type'],array('playlist','album','track','show','episode')) || $checkitem['id']=='')
			{

				//return response()->json(array('status'=>"failed",'msg'=>'Artist pick link is not correct!'), 200);

			}
			else
			{
				$realidtocheck=$checkitem['id'];
			}
		
		$checkitem2=SpotifyHelper::instance()->checkSpotifyItemId($realidtocheck,$itemid);
			if(!in_array($checkitem2['type'],array('playlist','album','track','show','episode')) || $checkitem2['id']=='')
			{
			return response()->json(array('status'=>"failed",'msg'=>'Artist pick link is not correct!'), 200);
			}

	$myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($itemid);
	
	SpotifyHelper::instance()->addItemToDB($myspotifyapi,$realidtocheck,$checkitem2['type']);
			

		// update artist's pick
		DB::table('spotify_accounts_auth_realartists')
		->where('managerid', '=', $itemid)
		->where('id', '=', $row_account2[0]->id)
		->update([
			'artistpick'=>$artistpick,
			'artistpickstate'=>$requestchange
			]);
		// update artist's pick


	return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);

	}

	

	public function getArtistPickSimple(Request $request) {

		$userid=Auth::id();
		
		$artistpick = $request->input('artistpick');
		$itemid = $request->input('itemid');

		$requestchange = $request->input('requestchange'); //0=done 1=waiting 2=getfromspotify 10=problem

		$row_get = DB::table('spotify_accounts_auth')
		->where(function($query) use ($userid,$request) {
			if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
				   $query->where('userid','=',$userid);
					 }
			})
		->where('active', '=', '1')
		->where('id', '=', $itemid)
		->get();


		foreach ($row_get as $row) {
			$row_account[]=$row;
		}

		if($row_account[0]->state!='1')
		{
			return response()->json(array('status'=>"failed",'msg'=>'Account is not yet connected to our system.'), 200);
		}

		if($row_account[0]->active!='1')
		{
			return response()->json(array('status'=>"failed",'msg'=>'Account is deactivated, you can\'t get artist\'s pick.'), 200);
		}

		if($row_account[0]->artistconnectid=='')
		{
			return response()->json(array('status'=>"failed",'msg'=>'You need to connect an artist first!'), 200);
		}

		if($row_account[0]->thingstr=='')
		{
			return response()->json(array('status'=>"failed",'msg'=>'You need to add spotify password first to get artist\'s pick!'), 200);
		}


		DB::table('spotify_accounts_auth_realartists')
		->where('managerid', '=', $itemid)
		->where('id', '=', $row_account[0]->artistconnectid)
		->update([
			'artistpickstate'=>'2'
			]);


		return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);


	}


	public function getSingleClaimState(Request $request) {


		$itemid = $request->input('itemid');
		

		$row_searchstring_get = DB::table('spotify_items')
					->where('id', '=', $itemid)
					->limit(1)
					->get();

					
					foreach ($row_searchstring_get as $row) {
						$search_cache_results[] = $row;
					}

					if(empty($search_cache_results) || $search_cache_results[0]->type!='artist')
					{

		return response()->json(array('status'=>"failed",'msg'=>'Failed!'), 200);

					}

					
	$claimstate=SpotifyHelper::instance()->getSingleArtistClaimState($search_cache_results[0]->itemid);
						



		return response()->json(array('status'=>"success",'msg'=>'Success!','claimstate'=>$claimstate), 200);

	}

	public function getMultipleClaimState(Request $request) {


		$userid = Auth::id();

		$inputdata = $request->input('inputdata');
		$firstpageelements = $request->input('firstpageelements');

		$search_string = $request->input('search_string');

		$allinputdata=$inputdata['value'];
		
		//$allinputdata['claimedrefresh']  claimed
		//$allinputdata['claimed2refresh']  claimed (set by us)
		//$allinputdata['notclaimedrefresh'] not claimed
		//$allinputdata['unknownrefresh']  unknown

		if(!in_array($allinputdata['refreshtype'],array('firstpage','allpages')))
		return response()->json(array('status'=>"failed",'msg'=>'Failed!'), 200);

		if($allinputdata['refreshtype']=='firstpage')
		{
				
			$i_c=0;
			$returnarray=array();
		foreach ($firstpageelements['itemids'] as $itemid) {

		$row_searchstring_get = DB::table('spotify_items')
					->where('id', '=', $itemid)
					->limit(1)
					->get();

					$search_cache_results=array();
					foreach ($row_searchstring_get as $row) {
						$search_cache_results[] = $row;
					}

			if(!$allinputdata['claimedrefresh'] && $search_cache_results[0]->claimed=='1')
					continue;

			if(!$allinputdata['claimed2refresh'] && $search_cache_results[0]->claimed=='3')
					continue;

			if(!$allinputdata['notclaimedrefresh'] && $search_cache_results[0]->claimed=='2')
					continue;

			if(!$allinputdata['unknownrefresh'] && $search_cache_results[0]->claimed=='0')
					continue;
					
					if(!empty($search_cache_results) && $search_cache_results[0]->type=='artist')
							{
			SpotifyHelper::instance()->getSingleArtistClaimState($search_cache_results[0]->itemid);
							}


							
					$row_searchstring_get2 = DB::table('spotify_items')
					->where('id', '=', $itemid)
					->limit(1)
					->get();
					$search_cache_results2=array();

					foreach ($row_searchstring_get2 as $row2) {
						$search_cache_results2[] = $row2;
					}

					$returnarray[$i_c]['itemid']=$itemid;
					$returnarray[$i_c]['claimed']=$search_cache_results2[0]->claimed;

					$i_c++;
					}

		return response()->json(array('status'=>"success",'msg'=>'Success!','returnarray'=>$returnarray), 200);
				
		}
		elseif($allinputdata['refreshtype']=='allpages')
		{


			$row_artists_claim_get = DB::table('spotify_artists_claim_queue')
					->where('searchstring', '=', base64_encode($search_string))
					->first();

					$artists_claim_id=$row_artists_claim_get->id;

					if($artists_claim_id>0)
					{

			return response()->json(array('status'=>"failed",'msg'=>'Failed cause already exists!'), 200);

					}


				DB::table('spotify_artists_claim_queue')
						->updateOrInsert(
					['searchstring' => $search_string],
					['item_count'=> 0,
					'item_left'=> 0,
					'userid'=>$userid,
					'claimedrefresh'=>$allinputdata['claimedrefresh'],
					'claimed2refresh'=>$allinputdata['claimed2refresh'],
					'notclaimedrefresh'=>$allinputdata['notclaimedrefresh'],
					'unknownrefresh'=>$allinputdata['unknownrefresh'],
					'artistswithoutgenres'=>$allinputdata['artistswithoutgenres'],
					'dt' => Carbon::now()]
							);

							$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_artists_claim_queue')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}



						
			return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);

		}

	}

	public function addNewGroup(Request $request) {


		$userid = Auth::id();

		$inputdata = $request->input('inputdata');

		$allinputdata=$inputdata['value'];
		

		if(!in_array($allinputdata['grouptype'],array('artist','playlist','track')))
		return response()->json(array('status'=>"failed1",'msg'=>'Failed: group type does not exist.'), 200);

		if($allinputdata['groupname']=='' || $allinputdata['groupdescription']=='')
		return response()->json(array('status'=>"failed2",'msg'=>'Failed: name and description is required.'), 200);
		

		$row_get = DB::table('spotify_groups')
	->where('name', '=', $allinputdata['groupname'])
	->limit(1)
	->get();

	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(!empty($result_check))
	{
return response()->json(array('status'=>"failed2",'msg'=>'Failed: a group already exists with this name!'), 200);
	}


		DB::table('spotify_groups')->insert(
			['name' => $allinputdata['groupname'], 
			'description' => $allinputdata['groupdescription'],
			'type' => $allinputdata['grouptype'],
			'dt' => Carbon::now(),
			'timestamp' => Carbon::now()->timestamp
			]
		);

						
			return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);

		

	}

	public function removeGroup(Request $request) {

		$userid = Auth::id();

		$itemid = urldecode($request->input('itemid'));

		$type = urldecode($request->input('type'));

		if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
			
			return response()->json(array('status'=>"failed",'msg'=>'You don\'t have the rights for this operation.'), 200);
			  }


	$row_get = DB::table('spotify_groups')
	->where('id', '=', $itemid)
	->limit(1)
	->get();

	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, group not found!'), 200);
	}

	
			DB::table('spotify_groups_'.$result_check[0]->type.'s_fk')
			->where('group_id', '=', $itemid)
			->delete();


			DB::table('spotify_groups')
              ->where('id', '=', $itemid)
			->delete();


			$groupsessid = \Session::get($result_check[0]->type.'lastgroupid');
			if($groupsessid==$itemid)
			{
				\Session::put($result_check[0]->type.'lastgroupname', '');
				\Session::put($result_check[0]->type.'lastgroupid', '');
			}
	




		return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);


	}

	public function removeFromGroup(Request $request) {

		$userid = Auth::id();

		$itemid = urldecode($request->input('itemid'));
		$groupid = urldecode($request->input('groupid'));
		$grouptype = urldecode($request->input('grouptype'));


		$row_get = DB::table('spotify_groups')
		->where('id', '=', $groupid)
		->limit(1)
		->get();
	
		foreach ($row_get as $row) {
			$result_check[] = $row;
		}
	
		if(empty($result_check))
		{
	return response()->json(array('status'=>"failed",'msg'=>'Failed, group not found!'), 200);
		}


				DB::table('spotify_groups_'.$grouptype.'s_fk')
					->where('group_id', '=', $groupid)
					->where('item_id', '=', $itemid)
					->delete();

	
			DB::table('spotify_groups')
                      ->where('id', '=', $groupid)
					  ->decrement('item_count', 1);


		return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);


	}

	public function removeMultipleFromGroup(Request $request) {

		$userid = Auth::id();

		$groupid = urldecode($request->input('groupid'));
		$grouptype = urldecode($request->input('grouptype'));
		$searchstring = urldecode($request->input('searchstring'));


		$row_get = DB::table('spotify_groups')
		->where('id', '=', $groupid)
		->limit(1)
		->get();
	
		foreach ($row_get as $row) {
			$result_check[] = $row;
		}
	
		if(empty($result_check))
		{
	return response()->json(array('status'=>"failed",'msg'=>'Failed, group not found!'), 200);
		}

$thesearchstringinput=$result_check[0]->searchstring;

$thesearchstringarray=explode('|',$thesearchstringinput);

$newsearchstringarray=array();
foreach ($thesearchstringarray as $thesearchstringarray_s)
{

	if($thesearchstringarray_s!=$searchstring)
	$newsearchstringarray[]=$thesearchstringarray_s;
}

$newsearchstring=trim(implode('|',array_unique($newsearchstringarray)),"|");

//get searchresult set

$search_string = $searchstring;

				
		$thesearchstring=base64_decode($search_string);

           $thesearchstring_expl=explode('_',$thesearchstring);

           $searchtype=$thesearchstring_expl[0];
		   $searchname=$thesearchstring_expl[1];
		   
		   $inputfolmin='';
		   $inputfolmax='';
		   $inputfolmin=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[2],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
           $inputfolmax=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[3],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);

		   $searchgenrestring=$thesearchstring_expl[4];

		   $this->claimedshow=$thesearchstring_expl[5];
		   $this->claimedshow2=$thesearchstring_expl[6];
		   $this->notclaimedshow=$thesearchstring_expl[7];
		   $this->unknownshow=$thesearchstring_expl[8];

		   $orderbyget=$thesearchstring_expl[9];

		   $this->artistswithoutgenres=$thesearchstring_expl[10];

		   $this->hidespotifyowned=$thesearchstring_expl[11];

		   if($orderbyget=='added')
		$orderby='t1.timestamp DESC';
		elseif($orderbyget=='followers')
		$orderby='t1.followercount DESC';
		elseif($orderbyget=='name')
		$orderby='t1.name ASC';
		else
		$orderby='t1.id ASC'; //default

		  
		   $searchgenre=array();
			$genre_ids=array();
			
			if($this->artistswithoutgenres=='1')
			$searchgenrestring='';


			if($searchtype=='playlist')
		{
			$this->artistswithoutgenres=0;
			$searchgenrestring='';
				$this->claimedshow=0;
				$this->claimedshow2=0;
				$this->notclaimedshow=0;
				$this->unknownshow=0;
		}

		if($searchtype=='artist')
		{
			$this->hidespotifyowned=0;
		}


            if($searchgenrestring !='')
				{
            $thegenrearray=explode(',',$searchgenrestring);
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
									$query->where('t1.name','LIKE','%'.$thegenrearray_s.'%');
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
					return response()->json(array('status'=>"failed",'msg'=>'Genres empty!'), 200);

				}

				$isfulltextsearch=1;
$theresultset=array();
                $offset=0;
                $elementincreaser=100;
                $try=true;
                while($try)
                {
                    $getresults_arr=array();

					$fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($searchname,1);

                $getresults = 
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
					->where(function($query)
						{
							if ($this->artistswithoutgenres=='1') {
								$query->where(function ($query) {
									$query->where('t1.genres', '=', '')
										->orWhereNull('t1.genres');
								});
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
							if ($this->claimedshow=='1') {
								$query->orWhere('t1.claimed','=',1);
							}
							if ($this->claimedshow2=='1') {
								$query->orWhere('t1.claimed','=',3);
							}
							if ($this->notclaimedshow=='1') {
								$query->orWhere('t1.claimed','=',2);
							}
							if ($this->unknownshow=='1') {
								$query->orWhere('t1.claimed','=',0);
							}
							
                        })
                    ->orderByRaw($orderby)
                    ->offset($offset)
                    ->limit($elementincreaser)
                    ->get();

						$result_notfound_count=0;
                    foreach ($getresults as $getresults_s)
						{
                           // $theresultset[]=$getresults_s;
							$getresults_arr[]=$getresults_s;
							

							$row_check_count = DB::table('spotify_groups_'.$grouptype.'s_fk')
							->where('group_id', '=', $groupid)
							->where('item_id', '=', $getresults_s->id)
							->limit(1)
							->get();
							$result_count_check=array();
							foreach ($row_check_count as $row_count) {
								$result_count_check[] = $row_count;
							}
							if(empty($result_count_check))
							$result_notfound_count=$result_notfound_count+1;

							if(!empty($result_count_check))
							{
//////////////////// remove from group
					DB::table('spotify_groups_'.$grouptype.'s_fk')
					->where('group_id', '=', $groupid)
					->where('item_id', '=', $getresults_s->id)
					->delete();

					DB::table('spotify_groups')
                      ->where('id', '=', $groupid)
					  ->decrement('item_count', 1);

//////////////////// remove from group
							}

                        }
						
						if(8000<$offset)//maximum resultset!  should be higher then the allowed!!
						$try=false;

                        $offset=$offset+$elementincreaser;

						if($result_notfound_count>1000) //resultset seems to be empty
						$try=false;

                        if(count(array_filter($getresults_arr))<$elementincreaser) //end of results
						$try=false;
						
                    }

//get searchresult set

	
			DB::table('spotify_groups')
            ->where('id', $groupid)
			->update(['searchstring' => $newsearchstring]);
			
			


		return response()->json(array('status'=>"success",'msg'=>'Success!'), 200);


	}

	


	public function getItemsGroups(Request $request) {

		

		$userid = Auth::id();

		$itemid = urldecode($request->input('itemid'));

		$type = urldecode($request->input('type'));

		

	$row_get = DB::table('spotify_items')
	->where('id', '=', $itemid)
	->limit(1)
	->get();

	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, item not found!'), 200);
	}

	
	$row_get = DB::table('spotify_groups AS t1')
	->select('t1.*')
      ->leftJoin('spotify_groups_'.$result_check[0]->type.'s_fk AS t2', function($join)
					{
				$join->on('t1.id', '=', 't2.group_id');
				   })
	->where('t1.type', '=', $type)
	->where('t2.item_id', '=', $itemid)
	->get();

	$addedgroups=array();
	foreach ($row_get as $row) {
		$addedgroups[] = $row;
	}


	$lastgroupid='';
	$lastgroupname='';
	
	if(\Session::get($result_check[0]->type.'lastgroupid')!=null)
	$lastgroupid=\Session::get($result_check[0]->type.'lastgroupid');
	if(\Session::get($result_check[0]->type.'lastgroupname')!=null)
	$lastgroupname=\Session::get($result_check[0]->type.'lastgroupname');


		return response()->json(array('status'=>"success",'msg'=>'Success!','addedgroups'=>$addedgroups,'lastgroupid'=>$lastgroupid,'lastgroupname'=>$lastgroupname), 200);


	}

	public function getMultipleItemsGroups(Request $request) {

		$userid = Auth::id();

		$searchstring = urldecode($request->input('searchstring'));

		$type = urldecode($request->input('type'));

		

	
	$row_get = DB::table('spotify_groups AS t1')
	->select('t1.*')
	->where('t1.searchstring', 'LIKE', $searchstring.'%')
	->where('t1.type', '=', $type)
	->get();

	$addedgroups=array();
	foreach ($row_get as $row) {
		$addedgroups[] = $row;
	}



		$lastgroupid='';
		$lastgroupname='';
		
		if(\Session::get($result_check[0]->type.'lastgroupid')!=null)
		$lastgroupid=\Session::get($result_check[0]->type.'lastgroupid');
		if(\Session::get($result_check[0]->type.'lastgroupname')!=null)
		$lastgroupname=\Session::get($result_check[0]->type.'lastgroupname');

		return response()->json(array('status'=>"success",'msg'=>'Success!','addedgroups'=>$addedgroups,'lastgroupid'=>$lastgroupid,'lastgroupname'=>$lastgroupname), 200);

	}


	public function suggestGroup(Request $request) {

		$userid=Auth::id();

		$searchterm=$request->input('term');
		
		$grouptype=$request->input('grouptype');

		$itemid=$request->input('itemid');

		$searchstring=$request->input('searchstring');

		if($itemid!='') //single
			{
				$row_get_groups = DB::table('spotify_groups_'.$grouptype.'s_fk AS t1')
				->where('t1.item_id','=',$itemid)
				->get();
				$grouparray=array();
				foreach ($row_get_groups as $row_groups) {
					$grouparray[]=$row_groups->group_id;
					
				}

			}
			else //multiple
			{
				$row_get_groups = DB::table('spotify_groups AS t1')
				->where('t1.searchstring','LIKE',$searchstring.'%')
				->get();
				$grouparray=array();
				foreach ($row_get_groups as $row_groups) {
					$grouparray[]=$row_groups->id;
					
				}
			}

			

        $row_get = DB::table('spotify_groups AS t1')
		->where('t1.name','LIKE',$searchterm.'%')
		->where('t1.type','=',$grouptype)
		->whereNotIn('t1.id',$grouparray)
        ->orderByRaw('t1.item_count DESC')
        ->offset(0)
		->limit(10)
		->get();

		foreach ($row_get as $row) {
            $results[]=$row;
            
		}

		$s=array();
		if(!empty($results))
		{
			foreach ($results as $results_s)
			{
				$s[] = array('label' => $results_s->name, 'value' => $results_s->id);
			}
		}
     


	return response()->json($s, 200);
			


	}


	public function addToGroup(Request $request) {

		$userid=Auth::id();

		$itemid = urldecode($request->input('itemid'));
		$groupid = urldecode($request->input('groupid'));

        $row_get = DB::table('spotify_groups AS t1')
		->where('t1.id','=',$groupid)
		->limit(1)
		->get();

		foreach ($row_get as $row) {
			$result_check[] = $row;
		}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed: group not found!'), 200);
	}

$grouptype=$result_check[0]->type;
$grouptitle=$result_check[0]->name;

$responsearray=array();
$responsearray['grouptype']=$grouptype;
$responsearray['groupid']=$groupid;
$responsearray['grouptitle']=$grouptitle;

	$row_get2 = DB::table('spotify_items AS t1')
		->where('t1.id','=',$itemid)
		->limit(1)
		->get();

		foreach ($row_get2 as $row2) {
			$result_check2[] = $row2;
		}

	if(empty($result_check2))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed: item not found!'), 200);
	}

	$itemtype=$result_check2[0]->type;


	if($itemtype!=$grouptype)
	{
		return response()->json(array('status'=>"failed",'msg'=>'Failed: item is not the same type as the group.'), 200);
	}


	$row_get3 = DB::table('spotify_groups_'.$result_check[0]->type.'s_fk')
		->where('item_id','=',$itemid)
		->where('group_id','=',$groupid)
		->limit(1)
		->get();

		foreach ($row_get3 as $row3) {
			$result_check3[] = $row3;
		}

	if(!empty($result_check3))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed: item already exists in that group!'), 200);
	}


	DB::table('spotify_groups_'.$result_check[0]->type.'s_fk')->updateOrInsert(
		['group_id' => $groupid, 
		 'item_id' => $itemid,
		]
	);


						DB::table('spotify_groups')
                      ->where('id', '=', $groupid)
					  ->increment('item_count', 1);

	\Session::put($result_check[0]->type.'lastgroupname', $grouptitle);
	\Session::put($result_check[0]->type.'lastgroupid', $groupid);

		return response()->json(array('status'=>"success",'msg'=>'Successful!','responsearray'=>$responsearray), 200);

	}


	public function addMultipleToGroup(Request $request) {

		$userid=Auth::id();

		$searchstring = urldecode($request->input('searchstring'));
		$groupid = urldecode($request->input('groupid'));

        $row_get = DB::table('spotify_groups AS t1')
		->where('t1.id','=',$groupid)
		->limit(1)
		->get();

		foreach ($row_get as $row) {
			$result_check[] = $row;
		}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed: group not found!'), 200);
	}


	$row_get2 = DB::table('spotify_groups AS t1')
	->where('t1.searchstring','LIKE',$searchstring.'%')
	->where('t1.id','=',$groupid)
	->limit(1)
	->get();

	foreach ($row_get2 as $row2) {
		$result_check2[] = $row2;
	}

if(!empty($result_check2))
{
return response()->json(array('status'=>"failed",'msg'=>'Failed: resultset already added to group!'), 200);
}

$grouptype=$result_check[0]->type;
$grouptitle=$result_check[0]->name;

$responsearray=array();
$responsearray['grouptype']=$grouptype;
$responsearray['groupid']=$groupid;
$responsearray['grouptitle']=$grouptitle;



$thesearchstringinput=$result_check[0]->searchstring;

$thesearchstringarray=explode('|',$thesearchstringinput);

$newsearchstringarray=array();
foreach ($thesearchstringarray as $thesearchstringarray_s)
{
	$newsearchstringarray[]=$thesearchstringarray_s;
}
$newsearchstringarray[]=$searchstring;

$newsearchstring=trim(implode('|',array_unique($newsearchstringarray)),"|");



//get searchresult set

$search_string = $searchstring;


				
		$thesearchstring=base64_decode($search_string);

           $thesearchstring_expl=explode('_',$thesearchstring);

		   $searchtype=$thesearchstring_expl[0];


	if($searchtype!=$grouptype)
	{
		return response()->json(array('status'=>"failed",'msg'=>'Failed: item is not the same type as the group.'), 200);
	}

		   $searchname=$thesearchstring_expl[1];
		   
		   $inputfolmin='';
		   $inputfolmax='';
		   $inputfolmin=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[2],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
           $inputfolmax=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[3],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);

		   $searchgenrestring=$thesearchstring_expl[4];

		   $this->claimedshow=$thesearchstring_expl[5];
		   $this->claimedshow2=$thesearchstring_expl[6];
		   $this->notclaimedshow=$thesearchstring_expl[7];
		   $this->unknownshow=$thesearchstring_expl[8];

		   $orderbyget=$thesearchstring_expl[9];

		   $this->artistswithoutgenres=$thesearchstring_expl[10];

		   $this->hidespotifyowned=$thesearchstring_expl[11];

		   if($orderbyget=='added')
		$orderby='t1.timestamp DESC';
		elseif($orderbyget=='followers')
		$orderby='t1.followercount DESC';
		elseif($orderbyget=='name')
		$orderby='t1.name ASC';
		else
		$orderby='t1.id ASC'; //default

		  
		   $searchgenre=array();
			$genre_ids=array();
			
			if($this->artistswithoutgenres=='1')
			$searchgenrestring='';

		if($searchtype=='playlist')
		{
			$this->artistswithoutgenres=0;
			$searchgenrestring='';
				$this->claimedshow=0;
				$this->claimedshow2=0;
				$this->notclaimedshow=0;
				$this->unknownshow=0;
		}

		if($searchtype=='artist')
		{
			$this->hidespotifyowned=0;
		}

			



            if($searchgenrestring !='')
				{
            $thegenrearray=explode(',',$searchgenrestring);
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
									$query->where('t1.name','LIKE','%'.$thegenrearray_s.'%');
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
					return response()->json(array('status'=>"failed",'msg'=>'Genres empty!'), 200);

				}


				$isfulltextsearch=1;
$theresultset=array();
                $offset=0;
                $elementincreaser=100;
                $try=true;
                while($try)
                {
                    $getresults_arr=array();

					$fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($searchname,1);

                $getresults = 
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
					->where(function($query)
						{
							if ($this->artistswithoutgenres=='1') {
								$query->where(function ($query) {
									$query->where('t1.genres', '=', '')
										->orWhereNull('t1.genres');
								});
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
							if ($this->claimedshow=='1') {
								$query->orWhere('t1.claimed','=',1);
							}
							if ($this->claimedshow2=='1') {
								$query->orWhere('t1.claimed','=',3);
							}
							if ($this->notclaimedshow=='1') {
								$query->orWhere('t1.claimed','=',2);
							}
							if ($this->unknownshow=='1') {
								$query->orWhere('t1.claimed','=',0);
							}
							
                        })
                    ->orderByRaw($orderby)
                    ->offset($offset)
                    ->limit($elementincreaser)
                    ->get();

					
                    foreach ($getresults as $getresults_s)
						{
                           // $theresultset[]=$getresults_s;
							$getresults_arr[]=$getresults_s;
							

							$row_check_count = DB::table('spotify_groups_'.$grouptype.'s_fk')
							->where('group_id', '=', $groupid)
							->where('item_id', '=', $getresults_s->id)
							->limit(1)
							->get();
							$result_count_check=array();
							foreach ($row_check_count as $row_count) {
								$result_count_check[] = $row_count;
							}

							if(empty($result_count_check))
							{
//////////////////// add to group

					  DB::table('spotify_groups_'.$grouptype.'s_fk')->updateOrInsert(
						['group_id' => $groupid, 
						 'item_id' => $getresults_s->id,
						]
					);
				
						DB::table('spotify_groups')
							  ->where('id', '=', $groupid)
							  ->increment('item_count', 1);

//////////////////// add to group
							}

                        }
						
						

						$offset=$offset+$elementincreaser;
						
						if(4900<$offset)//maximum resultset!
						$try=false;


                        if(count(array_filter($getresults_arr))<$elementincreaser) //end of results
						$try=false;
						
                    }

//get searchresult set

	
			DB::table('spotify_groups')
            ->where('id', $groupid)
			->update(['searchstring' => $newsearchstring]);



	\Session::put($result_check[0]->type.'lastgroupname', $grouptitle);
	\Session::put($result_check[0]->type.'lastgroupid', $groupid);

		return response()->json(array('status'=>"success",'msg'=>'Successful!','responsearray'=>$responsearray), 200);

	}
	

	public function downloadArtistResultset(Request $request) {

		
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 1000); // 600 = 10 minutes

		$userid = Auth::id();

		$search_string = $request->input('search_string_more');


				$this->claimedshow=$request->input('claimedrefresh');
                $this->claimedshow2=$request->input('claimed2refresh');
                $this->notclaimedshow=$request->input('notclaimedrefresh');
				$this->unknownshow=$request->input('unknownrefresh');

				
				
		$thesearchstring=base64_decode($search_string);

           $thesearchstring_expl=explode('_',$thesearchstring);

           $searchtype=$thesearchstring_expl[0];
		   $searchname=$thesearchstring_expl[1];
		   
		   $inputfolmin='';
		   $inputfolmax='';
		   $inputfolmin=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[2],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
           $inputfolmax=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[3],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);

		   $searchgenrestring=$thesearchstring_expl[4];

		   $this->claimedshow=$thesearchstring_expl[5];
		   $this->claimedshow2=$thesearchstring_expl[6];
		   $this->notclaimedshow=$thesearchstring_expl[7];
		   $this->unknownshow=$thesearchstring_expl[8];

		   $orderbyget=$thesearchstring_expl[9];

		   $this->artistswithoutgenres=$thesearchstring_expl[10];




		   if($orderbyget=='added')
		$orderby='t1.timestamp DESC';
		elseif($orderbyget=='followers')
		$orderby='t1.followercount DESC';
		elseif($orderbyget=='name')
		$orderby='t1.name ASC';
		else
		$orderby='t1.id ASC'; //default

		   if($searchtype!='artist')
           {
			return response()->json(array('status'=>"failed",'msg'=>'Not an artist or follower input not good!'), 200);
		   }

		   $searchgenre=array();
			$genre_ids=array();
			
			if($this->artistswithoutgenres=='1')
			$searchgenrestring='';

            if($searchgenrestring !='')
				{
            $thegenrearray=explode(',',$searchgenrestring);
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
									$query->where('t1.name','LIKE','%'.$thegenrearray_s.'%');
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
					return response()->json(array('status'=>"failed",'msg'=>'Genres empty!'), 200);


				}
				
//////////////////// start excel
$fieldheader[]='Name';
$fieldheader[]='Followers';
$fieldheader[]='State';
$fieldheader[]='Link';
$fieldheader[]='Added to DB (GMT)';
$fieldheader[]='Note';
$fieldheader[]='Genres';
$fieldheader[]='Distributor';

$folderToPutExcel="../storage/downloads/excelresultset/";	
$fileToDownload='artist_resultset_'.date('Y-m-d_H_i_s').'.csv';
if(false === ( $fp = fopen($folderToPutExcel.$fileToDownload, 'w') ) ) {
	
}
else
{
	
}
$sep  = "\t";
$eol  = "\n";
$csv  =  count($fieldheader) ? '"'. implode('"'.$sep.'"', $fieldheader).'"'.$eol : '';
$encoded_csv = mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
			fwrite($fp, chr(255) . chr(254) . $encoded_csv);
//////////////////// start excel


$isfulltextsearch=1;
				$theresultset=array();
                $offset=0;
                $elementincreaser=100;
                $try=true;
                while($try)
                {
					$getresults_arr=array();
					
					$fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($searchname,1);

                $getresults = 
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
					->where(function($query)
						{
							if ($this->artistswithoutgenres=='1') {
								$query->where(function ($query) {
									$query->where('t1.genres', '=', '')
										->orWhereNull('t1.genres');
								});
							}
						})
					->where('t1.type','=',$searchtype)
					->where('t1.followercount', '>=', $inputfolmin)
					->where('t1.followercount', '<=', $inputfolmax)
					->where(function($query)
						{
							if ($this->claimedshow=='1') {
								$query->orWhere('t1.claimed','=',1);
							}
							if ($this->claimedshow2=='1') {
								$query->orWhere('t1.claimed','=',3);
							}
							if ($this->notclaimedshow=='1') {
								$query->orWhere('t1.claimed','=',2);
							}
							if ($this->unknownshow=='1') {
								$query->orWhere('t1.claimed','=',0);
							}
							
                        })
                    ->orderByRaw($orderby)
                    ->offset($offset)
                    ->limit($elementincreaser)
                    ->get();


                    foreach ($getresults as $getresults_s)
						{
                           // $theresultset[]=$getresults_s;
							$getresults_arr[]=$getresults_s;
							


//////////////////// append to excel
$all_export_result_s=array();
$csv='';
$all_export_result_s[]=$getresults_s->name;
$all_export_result_s[]=$getresults_s->followercount;

if($getresults_s->claimed=='1')
	$all_export_result_s[]='Claimed';
elseif($getresults_s->claimed=='2')
	$all_export_result_s[]='Not Claimed';
elseif($getresults_s->claimed=='3')
	$all_export_result_s[]='Claimed (changed)';
else
	$all_export_result_s[]='Unknown';

	$all_export_result_s[]=$getresults_s->url;
	$all_export_result_s[]=date('d/m/Y H:i', $getresults_s->timestamp);
	$all_export_result_s[]=$getresults_s->note;
	$all_export_result_s[]=$getresults_s->genres;
	$all_export_result_s[]=$getresults_s->distributorname;
			
$csv .= '"'. implode('"'.$sep.'"', $all_export_result_s).'"'.$eol;
$encoded_csv = mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
			fwrite($fp, chr(255) . chr(254) . $encoded_csv);
						
//////////////////// append to excel

                        }
						
						
						if(100000<$offset)//maximum resultset!
						$try=false;

                        $offset=$offset+$elementincreaser;

						
                        if(count(array_filter($getresults_arr))<$elementincreaser) //end of results
                        $try=false;

                    }


                    $theresultset=array_filter($theresultset);
					$resultsetcount=count($theresultset);

//////////////////// close excel
			
			fclose($fp);
//////////////////// close excel
return response()->json(array('status'=>"success",'msg'=>'Success!','dlurl'=>config('myconfig.config.server_url').'admin/downloadcsv?file='.$fileToDownload), 200);
exit;
                        if($resultsetcount==0)
                        {
                            return response()->json(array('status'=>"failed",'msg'=>'No results found!'), 200);
                        }

			$count_item=0;
			foreach($theresultset as $theresultset_s)
			{
				
				$all_export_result[$count_item][]=$theresultset_s->name;
				$all_export_result[$count_item][]=$theresultset_s->followercount;

				if($theresultset_s->claimed=='1')
				$all_export_result[$count_item][]='Claimed';
				elseif($theresultset_s->claimed=='2')
				$all_export_result[$count_item][]='Not Claimed';
				elseif($theresultset_s->claimed=='3')
				$all_export_result[$count_item][]='Claimed (changed)';
				else
				$all_export_result[$count_item][]='Unknown';

				$all_export_result[$count_item][]=$theresultset_s->url;
				$all_export_result[$count_item][]=date('d/m/Y H:i', $theresultset_s->timestamp);
				$all_export_result[$count_item][]=$theresultset_s->note;
				$all_export_result[$count_item][]=$theresultset_s->genres;
				$all_export_result[$count_item][]=$theresultset_s->distributorname;


				$count_item++;
			}

			$fieldheader[]='Name';
			$fieldheader[]='Followers';
			$fieldheader[]='State';
			$fieldheader[]='Link';
			$fieldheader[]='Added to DB (GMT)';
			$fieldheader[]='Note';
			$fieldheader[]='Genres';
			$fieldheader[]='Distributor';

			$folderToPutExcel="../storage/downloads/excelresultset/";	
			$fileToDownload='artist_resultset_'.date('Y-m-d_H_i_s').'.csv';
			if(false === ( $fp = fopen($folderToPutExcel.$fileToDownload, 'w') ) ) {
				
			}
			else
			{
				
			}

			$sep  = "\t";
			$eol  = "\n";
			$csv  =  count($fieldheader) ? '"'. implode('"'.$sep.'"', $fieldheader).'"'.$eol : '';

			foreach ($all_export_result as $all_export_result_s)
			{
				//fputcsv($fp,  array_map("utf8_decode", $all_export_result_s), ';', '"');
				$csv .= '"'. implode('"'.$sep.'"', $all_export_result_s).'"'.$eol;
			}

			$encoded_csv = mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
			
			fwrite($fp, chr(255) . chr(254) . $encoded_csv);
			
			fclose($fp);


			return response()->json(array('status'=>"success",'msg'=>'Success!','dlurl'=>config('myconfig.config.server_url').'admin/downloadcsv?file='.$fileToDownload), 200);

	}
	
	public function titletransformer(Request $request) {
		
		$userid=Auth::id();
		
		$videorealurl = Helperfunctions::instance()->cleanURL(Helperfunctions::instance()->cleanURLMinimal(urldecode($request->input('title'))));
		
		
		$i = 1; $baseurl = $videorealurl;
			while(Helperfunctions::instance()->check_into_database($videorealurl)){
				$videorealurl = $baseurl . "-" . $i++;        
			}
		
		echo $videorealurl;
		
				//return response()->json(array('message' =>'uploadproblem'), 200);

			
	}
	
	public function morevideoinfo(Request $request) {
		
		$userid=Auth::id();
		
		
		$videorealurl = Helperfunctions::instance()->cleanURL( Helperfunctions::instance()->cleanURLMinimal(urldecode($request->input('title'))));
			
			
			$i = 1; $baseurl = $videorealurl;
			while(Helperfunctions::instance()->check_into_database($videorealurl)){
				$videorealurl = $baseurl . "-" . $i++;        
			}
			//check for unique url
			
			if($request->input('title')!='')
		{
			$decodedtitle= urldecode($request->input('title'));
			
			
			if ($quotation_mark == 'yes')
			{
				$titledecoded = '"'.addslashes($decodedtitle).'"';
			}
			else
			{
				//$decodedtitle= str_replace(array("'", "\"", "&quot;"), "",$decodedtitle);
				//$decodedtitle= str_replace(array("'", "\"", "&quot;"), "",$decodedtitle);
				$titledecoded = addslashes($decodedtitle);
			}
			
						$content = substr(strip_tags($titledecoded), 0, 255);
						$query_words = str_replace("+", " ", $content);		
						$query_words = str_replace(",", " ", $query_words);
						$query_words = str_replace("       ", " ", $query_words);
						$query_words = str_replace("      ", " ", $query_words);
						$query_words = str_replace("     ", " ", $query_words);
						$query_words = str_replace("    ", " ", $query_words);
						$query_words = str_replace("   ", " ", $query_words);
						$query_words = str_replace("  ", " ", $query_words);
						$query_words = str_replace(" ", " ", $query_words);
					
						$array = $query_words;
						$array = explode(' ', $array );
						/*foreach ($array as &$value){
							$value = ' '.$value;
						}*/
						foreach ($array as &$value){
							$value = $value.'*';
						}
						$query_words1=implode(' ', $array);
		
		}
		
		
		$items_get = DB::table('videos AS t1')
			->select('t1.*',DB::raw('MATCH (t1.title) AGAINST ("'.$query_words1.'" IN BOOLEAN MODE) score'), 't2.imagelink')
			
										->leftJoin('channels AS t2', function($join)
                        				{
										$join->on('t1.channelid', '=', 't2.channelid');
										})
										->where('active', '=', 1)
										->havingRaw('score > 0')
										->orderBy('score','DESC')
										->GroupBy('t1.channelid')
										->get();
							
		
		foreach ($items_get as $row) {
										
								$items[] = $row;		
			
								$possibleuploads.='<a title="'.$row->title.'" target="_blank" href="'.$row->videourl.'"><img src="'.config('myconfig.config.server_url').'images/'.$row->imagelink.'" /></a>';
										
									}
		
		
		
						if ($possibleuploads=='')
						{
						$possibleuploads='<span>This song hasn\'t been uploaded yet.</span>';
						}
						
						
		
		return response()->json(array('transformed'=>$videorealurl,'possibleuploads'=>$possibleuploads), 200);
		
	}
	
	public function changevideo(Request $request) {
		
		$userid=Auth::id();
		
		$videoid = $request->input('videoid');

		$change = $request->input('change');
		
		
		if($change=='deactivate')
		{
			
			DB::table('othersvideos')
            ->where('id', $videoid)
            ->update(['active' => 0]);
			
		}
		elseif($change=='activate')
		{
			DB::table('othersvideos')
            ->where('id', $videoid)
            ->update(['active' => 1]);
		}
		elseif($change=='uploadedadd')
		{
		
			DB::table('uploadedcheck')
    		->updateOrInsert(
        ['userid' => $userid, 'videoid' => $videoid],
        ['uploaded' => '1']
    			);
			
			
		}
		elseif($change=='uploadedremove')
		{
			
			DB::table('uploadedcheck')
    		->updateOrInsert(
        ['userid' => $userid, 'videoid' => $videoid],
        ['uploaded' => '0']
				);
			
		}
		elseif($change=='archivecontact')
		{
			DB::table('contacts')
            ->where('id', '=', $videoid)
            ->update(['active' => 0]);
		}
		elseif($change=='removecontact')
		{
				
			
			$query = "SELECT * FROM contacts WHERE id=?";

					$result = DB::select($query, array($videoid));
						
						$submitformlink=$result[0]->file;
						$submitmp3link=$result[0]->file_mp3;
				
			
			$submitform = parse_url($submitformlink, PHP_URL_PATH);
			$form_file=basename($submitform);
			
			$submitmp3 = parse_url($submitmp3link, PHP_URL_PATH);
			$mp3_file=basename($submitmp3);
		
			@unlink(public_path()."/"."upload/submitted_forms/".form_file);
			@unlink(public_path()."/"."upload/submitted_mp3s/".$mp3_file);
			
			
			DB::table('contacts')
				->where('id', '=', $videoid)
				->delete();
			
		}
		elseif($change=='archiveremove')
		{
			DB::table('contacts')
            ->where('id', '=', $videoid)
            ->update(['active' => 1]);
		}
		
		
	}
	
	public function adduser(Request $request) {
		
		$userid=Auth::id();
		
		$username=$request->input('username');
		
		//check if username longer then x characters

		//usernamecheck
			if((strlen(trim($username)) != strlen($username)) || (strlen($username)<4) || (strlen($username)>84))
			{
				$commentresponse='<span class="failed">Username should not contain spaces, and length should be between 4 and 84 characters!</span>';

				echo $commentresponse;
								exit;

			}
		//usernamecheck
		
		$email=$request->input('email');
		//check if email valid
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		  $emailnotvalid='1';
		} else {
		  $emailnotvalid='0';
		}

		if($emailnotvalid=='0' || $email=='')
		{
			$commentresponse='<span class="failed">Email is not valid!</span>';

			echo $commentresponse;
								exit;

		}

			
		//check if email exist
		$emailexists='0';
		
		
		$emailexist_query = DB::table('users')
    ->where('email', '=', urldecode($email))
    ->first();

		if (is_null($emailexist_query)) {
			// It does not exist
		} else {
			// It exists
			$emailexists='1';
		}
	

		if($emailexists=='1')
		{
			$commentresponse='<span class="failed">Email address already added!</span>';

			echo $commentresponse;
								closedb();
								exit;

		}
		
		$realurl =  substr(Helperfunctions::instance()->cleanURLMinimal(mb_strtolower($username)), 0, 84);

						$i = 1; $baseurl = $realurl;
							while(Helperfunctions::instance()->checkurluser($realurl)){
								$realurl = $baseurl . "-" . $i++;        
							}

		$insertedid=DB::table('users')->insertGetId(
    ['username' => $realurl, 'email' => $email, 'type' => 'editor']
);
		
					if($insertedid!='' && $insertedid!==null && is_numeric($insertedid))
					$commentresponse='good';
					else
					$commentresponse='<span class="failed">Failed to add user</span>';

			
						echo $commentresponse;
						exit;
		
		
		
	}
	
	public function changeuser(Request $request) {
		
		$userid=Auth::id();
		
		$useridtoremove = $request->input('userid');

		$change = $request->input('change');
				
		if($change=='removeuser')
		{
			
			$userexist_query = DB::table('users')
			->where('id', '=', $useridtoremove)
			->first();

				if (is_null($userexist_query)) {
					// It does not exist
				} else {
					// It exists
					
					if($userexist_query->type!='admin')
					{
						
						DB::table('users')
						->where('id', '=', $useridtoremove)
						->delete();
						
					}
				}
			
				
			
		}
		
		
	}
	
	public function converter2(Request $request) {
		
		$userid=Auth::id();
		
		if($request->input('vidurl')=='') {
		return response()->json(array('message'=>"problem"), 200);
		exit;
	}
		
// Downloading HD Videos may take some time.
ini_set('max_execution_time', 600);
// Writing HD Videos to your disk, may need some extra resources.
ini_set('memory_limit', '64M');

$link = $request->input('vidurl');
		
		
		$tmp_id = Helperfunctions::instance()->parse_yturl($link);
        $vid_id = ($tmp_id !== FALSE) ? $tmp_id : $link;	
		
		$checkurl = sprintf("https://www.youtube.com/watch?v=%s", $vid_id);
        if(Helperfunctions::instance()->curl_httpstatus($checkurl) !== 200) {
			return response()->json(array('message'=>"invalid_youtube_id"), 200);
			exit;
		}
		
		
		DB::statement("SET SESSION wait_timeout = 3600");
		
		$query_conv_data = DB::table('converted_data')
			->where('youtubeid', '=', $vid_id)
			->orderBy('id', 'DESC')
			->first();
		
		if (is_null($query_conv_data)) {
					// It does not exist
			$resulttext="notfound";
			
				} else {
					// It exists
					
					if($query_conv_data->active=='1')
					{
						$resulttext="activefound";
						
					}
					else
					{
						$resulttext="inactivefound";
					}
				}
		
		
		$video_url="https://www.youtube.com/watch?v=".$vid_id;
		
		
		
	 //$cmd = '/usr/local/bin/youtube-dl -4 -o "/home/designbr/public_html/test/storage/app/downloadwav/%(id)s.%(ext)s" '.$video_url.' --extract-audio --audio-format wav --ffmpeg-location /usr/local/bin/ffmpeg';
	 
	 	$cmd = '/usr/local/bin/youtube-dl -4 -o "'.storage_path('app/downloadwav/').'%(id)s.%(ext)s" '.$video_url.' --extract-audio --audio-format wav --ffmpeg-location /usr/local/bin/ffmpeg';
		
		
		/*
		##Debug
	exec($cmd.' 2>&1', $output, $return_var);
	var_dump($return_var);
	echo "return_var is: $return_var" . "\n";
	var_dump($output);
	exit;*/
		
		
		if(($resulttext!='activefound') && ($resulttext!='inactivefound'))
		{
      // Execute yl-download command line command.
     $executeyoutubedl = exec($cmd);
		}
	
				if(($resulttext!='activefound') && ($resulttext!='inactivefound'))
					{

				$apikey = config('myconfig.youtube.youtube_api_key');

			   $videoTitle = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=$vid_id&key=$apikey&fields=items(id,snippet(title),contentDetails)&part=snippet,contentDetails");

			   if ($videoTitle) {
			$json = json_decode($videoTitle, true);

				   if (!empty($json['items'])) {
				$title = $json['items'][0]['snippet']['title'];
				   }
				   else
				   {
						return response()->json(array('message'=>"youtubeurlstatfailed"), 200);
						exit;
				   }

			} else {
					return response()->json(array('message'=>"youtubeurlstatfailed"), 200);
					exit;
			}

				}
					else
					{
					$title = $query_conv_data->title;
					}
		
		$hash=Helperfunctions::instance()->random_string(40);
		
		if($resulttext=='activefound')
		{
		$dlurl='?h='.$query_conv_data->hash."&vid=".$vid_id;
		}
		else
		{
		$dlurl='?h='.$hash."&vid=".$vid_id;
		}
		
		//converted_data
		if($resulttext=='activefound')
		{
			
			DB::table('converted_data')
            ->where('id', '=', $query_conv_data->id)
            ->update(['date' => time()]);
			
		}
		elseif($resulttext=='inactivefound')
		{
			
			DB::table('converted_data')
            ->where('id', '=', $query_conv_data->id)
            ->update(['hash' => $hash, 'date'=>time(), 'active'=>'1']);
			
		}
		else 
		{
			
			$query_conv_data = DB::table('converted_data')
			->where('youtubeid', '=', $vid_id)
			->orderBy('id', 'DESC')
			->first();
		
		if (is_null($query_conv_data)) {
					// It does not exist
			
			
			DB::table('converted_data')->insert(
    ['title' => $title, 'hash' => $hash, 'date' => time(), 'youtubeid' => $vid_id, 'active' => '1']
);
			
			
				} else {
					// It exists
					
					$converted_data2 = $query_conv_data;
					$dlurl='?h='.$query_conv_data->hash."&vid=".$vid_id;
			
				}
			
		
			
			
		}
		//converted_data
		//log
		
		DB::table('convert_log')->insert(
    ['ip' => $request->ip(), 'date' => time(), 'youtubeid' => $vid_id]
);
			
		//log
		
		
		return response()->json(array('message'=>"good",'title'=>$title,'imageurl'=>$imageurl, 'dlurl'=>$dlurl), 200);
		
	}


	public function generateArtistCode(Request $request) {

		$itemid = urldecode($request->input('itemid'));
		$userid=Auth::id();

	$randomhash=Helperfunctions::instance()->random_string(40);

	

	$row_get = DB::table('spotify_accounts_auth')
	->where('id', '=', $itemid)
	->where('active', '=', '1')
	->where(function($query) use ($userid,$request) {
		if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
			   $query->where('userid','=',$userid);
				 }
		})
	->limit(1)
	->get();

	
	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{

return response()->json(array('status'=>"failed",'msg'=>'Failed!'), 200);

	}

	$userid=$result_check[0]->userid;

	DB::table('spotify_accounts_auth')
	->where('id', '=', $itemid)
	->where('userid', '=', $userid)
	->update(['generatedstr' => $randomhash]);



	return response()->json(array('status'=>"success",'generatedlink'=>$randomhash), 200);






	}

	public function removeManager(Request $request) {
	
		$itemid = urldecode($request->input('itemid'));

		$userid=Auth::id();

		if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
			
			return response()->json(array('status'=>"failed",'msg'=>'You don\'t have the rights for this operation.'), 200);
			  }


	$row_get = DB::table('spotify_accounts_auth')
	->where('id', '=', $itemid)
	->limit(1)
	->get();

	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, manager account not found!'), 200);
	}

	if($result_check[0]->artistconnectid!='' && $result_check[0]->artistconnectid>0)
	{
		//remove artist
		$row_get_art = DB::table('spotify_accounts_auth_realartists')
			->where('id', '=', $result_check[0]->artistconnectid)
			->get();

			foreach ($row_get_art as $row_art) {

				$this->removeArtistWithId($itemid,$row_art->spid);

			}
		
		//remove artist
	}


	if($result_check[0]->playlistcount>0)
	{
		//remove playlists
		$row_get_pl = DB::table('spotify_accounts_auth_realplaylists')
			->where('managerid', '=', $itemid)
			->get();

			foreach ($row_get_pl as $row_pl) {

				$this->removePlaylistWithId($row_pl->spid);

			}
		//remove playlists
	}




DB::table('spotify_accounts_auth')
	->where('id', '=', $itemid)
	->delete();


			  return response()->json(array('status'=>"success"), 200);


	}

	public function removeArtist(Request $request) {
		
		$itemid = urldecode($request->input('itemid'));
		$artistid = urldecode($request->input('artistid'));

		$userid=Auth::id();

		if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
			
			return response()->json(array('status'=>"failed",'msg'=>'You don\'t have the rights for this operation.'), 200);
			  }


	$row_get_man = DB::table('spotify_accounts_auth')
		->where('id', '=', $itemid)
		->limit(1)
		->get();

	foreach ($row_get_man as $row_man) {
		$result_check_man[] = $row_man;
	}

	if(empty($result_check_man))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, manager account not found!'), 200);
	}

	$row_get = DB::table('spotify_accounts_auth_realartists')
	->where('spid', '=', $artistid)
	->limit(1)
	->get();

	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, artist not found!'), 200);
	}

	DB::table('spotify_accounts_auth_realartists')
	->where('spid', '=', $artistid)
	->delete();


	DB::table('spotify_accounts_auth')
	->where('id', '=', $itemid)
	->update(['artistconnectid' => NULL]);



			  return response()->json(array('status'=>"success"), 200);


	}

	public function removeArtistWithId($itemid,$artistid) {
		
		$userid=Auth::id();

	$row_get_man = DB::table('spotify_accounts_auth')
		->where('id', '=', $itemid)
		->limit(1)
		->get();

	foreach ($row_get_man as $row_man) {
		$result_check_man[] = $row_man;
	}

	if(empty($result_check_man))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, manager account not found!'), 200);
	}

	$row_get = DB::table('spotify_accounts_auth_realartists')
	->where('spid', '=', $artistid)
	->limit(1)
	->get();

	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, artist not found!'), 200);
	}

	DB::table('spotify_accounts_auth_realartists')
	->where('spid', '=', $artistid)
	->delete();


	DB::table('spotify_accounts_auth')
	->where('id', '=', $itemid)
	->update(['artistconnectid' => NULL]);



			  return response()->json(array('status'=>"success"), 200);


	}

	public function removePlaylist(Request $request) {

		
		$itemid = urldecode($request->input('itemid'));
		$playlistid = urldecode($request->input('playlistid'));

		$userid=Auth::id();

		if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
			
			return response()->json(array('status'=>"failed",'msg'=>'You don\'t have the rights for this operation.'), 200);
			  }

			

	 $row_get = DB::table('spotify_accounts_auth_realplaylists')
	->where('spid', '=', $playlistid)
	->limit(1)
	->get();

	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, playlist not found!'), 200);
	}

	/*
	if($result_check[0]->type !='genreplaylist')
	{
		return response()->json(array('status'=>"failed",'msg'=>'That type is not allowed!'), 200);
	}
	*/

	$managerid=$result_check[0]->managerid;

	$myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($managerid);


			$try=true;
			while($try)
			{
				try {
					
						$unfollowplaylist = $myspotifyapi->unfollowPlaylist($playlistid);

					$try=false;
				} catch (\Exception $e) {
					


						if ($e->getCode() == 429) {

							echo 'Rate limit, trying again:'."\n";
							
						}
						else
						{
							
							echo 'problem:'.$e->getCode();
						return 'problem';
						$try=false;
							exit;
						}
						
				}
			}

				if($unfollowplaylist)
				{

	DB::table('spotify_accounts_auth_realplaylists')
	->where('spid', '=', $playlistid)
	->delete();


	$row_items = DB::table('spotify_items')
	->where('itemid', '=', $playlistid)
	->where('type', '=', 'playlist')
	->limit(1)
	->get();

	foreach ($row_items as $row_item) {
		$theitem = $row_item;

		if($theitem!='')
		{


			$row_groups = DB::table('spotify_groups_playlists_fk')
			->where('item_id', '=', $theitem->id)
			->limit(1)
			->get();
			foreach ($row_groups as $rows_group) {
			

				DB::table('spotify_groups_playlists_fk')
				->where('item_id', '=', $theitem->id)
				->delete();


				DB::table('spotify_groups')
                      ->where('id', '=', $rows_group->group_id)
					  ->decrement('item_count', 1);

			}


	

	DB::table('spotify_itemkeyword_fk')
	->where('item_id', '=', $theitem->id)
	->delete();



			DB::table('spotify_items')
	->where('id', '=', $theitem->id)
	->delete();


		}
	}

	

						DB::table('spotify_genres')
                      ->where('playlistid', '=', $result_check[0]->id)
					  ->update(['playlistid' => null]);

DB::table('spotify_accounts_auth')
                      ->where('id', '=', $managerid)
					  ->decrement('playlistcount', 1);



					  
					  return response()->json(array('status'=>"success"), 200);

					}
	
	}

	public function removePlaylistWithId($playlistid) {

		$userid=Auth::id();

	 $row_get = DB::table('spotify_accounts_auth_realplaylists')
	->where('spid', '=', $playlistid)
	->limit(1)
	->get();

	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{
return response()->json(array('status'=>"failed",'msg'=>'Failed, playlist not found!'), 200);
	}

	/*
	if($result_check[0]->type !='genreplaylist')
	{
		return response()->json(array('status'=>"failed",'msg'=>'That type is not allowed!'), 200);
	}
	*/

	$managerid=$result_check[0]->managerid;

	$myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($managerid);


			$try=true;
			while($try)
			{
				try {
					
						$unfollowplaylist = $myspotifyapi->unfollowPlaylist($playlistid);

					$try=false;
				} catch (\Exception $e) {
					


						if ($e->getCode() == 429) {

							echo 'Rate limit, trying again:'."\n";
							
						}
						else
						{
							
							echo 'problem:'.$e->getCode();
						return 'problem';
						$try=false;
							exit;
						}
						
				}
			}

				if($unfollowplaylist)
				{

	DB::table('spotify_accounts_auth_realplaylists')
	->where('spid', '=', $playlistid)
	->delete();


	$row_items = DB::table('spotify_items')
	->where('itemid', '=', $playlistid)
	->where('type', '=', 'playlist')
	->limit(1)
	->get();

	foreach ($row_items as $row_item) {
		$theitem = $row_item;

		if($theitem!='')
		{


			$row_groups = DB::table('spotify_groups_playlists_fk')
			->where('item_id', '=', $theitem->id)
			->limit(1)
			->get();
			foreach ($row_groups as $rows_group) {
			

				DB::table('spotify_groups_playlists_fk')
				->where('item_id', '=', $theitem->id)
				->delete();


				DB::table('spotify_groups')
                      ->where('id', '=', $rows_group->group_id)
					  ->decrement('item_count', 1);

			}


	

	DB::table('spotify_itemkeyword_fk')
	->where('item_id', '=', $theitem->id)
	->delete();



			DB::table('spotify_items')
	->where('id', '=', $theitem->id)
	->delete();


		}
	}

	

						DB::table('spotify_genres')
                      ->where('playlistid', '=', $result_check[0]->id)
					  ->update(['playlistid' => null]);

DB::table('spotify_accounts_auth')
                      ->where('id', '=', $managerid)
					  ->decrement('playlistcount', 1);



					  
					  return response()->json(array('status'=>"success"), 200);

					}
	
	}

	public function addSimpleArtist(Request $request) {

		$itemid = urldecode($request->input('itemid'));
		$artistid = urldecode($request->input('artistid'));

		$userid=Auth::id();
		

		$row_get = DB::table('spotify_accounts_auth as t1')
        ->select('t1.*','t3.spid AS artistid','t3.userid AS ownerid')
       ->leftJoin('spotify_accounts_auth_realartists AS t3', function($join)
		{
				$join->on('t1.artistconnectid', '=', 't3.id');
		})
	->where('t1.active', '=', '1')
	->where('t1.id', '=', $itemid)
	->where(function($query) use ($userid,$request) {
		if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
			   $query->where('t1.userid','=',$userid);
				 }
		})
	->limit(1)
	->get();

	
	foreach ($row_get as $row) {
		$result_check[] = $row;
	}


	if($result_check[0]->userid=='0')
	{

return response()->json(array('status'=>"failed",'msg'=>'You need to add this generated account to a user first!'), 200);

	}


	if ((Auth::user()->isAdmin() || Auth::user()->isEditor()) && $result_check[0]->ownerid!='') {
		$userid=$result_check[0]->userid;
	}


	if((!Auth::user()->isAdmin() && !Auth::user()->isEditor()) && (empty($result_check) || ($result_check[0]->ownerid!='' && $result_check[0]->ownerid!=$userid)))
	{

return response()->json(array('status'=>"failed",'msg'=>'You are not the owner, only the owner can add/change artist.'), 200);

	}

	if($result_check[0]->spid==$artistid)
	{

return response()->json(array('status'=>"failed",'msg'=>'You can\'t add the same account as artist.'), 200);

	}

	if($result_check[0]->state!='1')
	{
		return response()->json(array('status'=>"failed",'msg'=>'Account is not yet connected to our system.'), 200);
	}

	if($result_check[0]->active!='1')
	{
		return response()->json(array('status'=>"failed",'msg'=>'Account is deactivated, you can\'t add artist.'), 200);
	}

	if($result_check[0]->spid==$artistid)
	{

return response()->json(array('status'=>"failed",'msg'=>'You can\'t add your manager account as an artist.'), 200);

	}

	if($result_check[0]->artistid==$artistid)
	{

return response()->json(array('status'=>"success"), 200);

	}



	

	$checkitem=SpotifyHelper::instance()->getSpotifyArtistItemId($artistid);
		if(!in_array($checkitem['type'],array('artist')) || $checkitem['id']=='')
		{
			//return response()->json(array('status'=>"failed",'msg'=>'Artist link or id is not correct!'), 200);
			
		}
		else
		{
			$artistid=$checkitem['id'];
		}

		$checkitem2=SpotifyHelper::instance()->checkArtistItemId($artistid,$itemid);
			if(!in_array($checkitem2['type'],array('artist')) || $checkitem2['id']=='')
			{
			return response()->json(array('status'=>"failed",'msg'=>'Artist link or id is not correct!'), 200);
			}

	

	$spotifyapi=SpotifyHelper::instance()->getSpotifyTokens();
	try {
		
		$artist=$spotifyapi->getArtist($artistid);
		
	} catch (\Exception $e) {

		return response()->json(array('status'=>"failed",'msg'=>'Not an artist!'), 200);

	}

	

	$imageurl='';
if($artist->images[2]->url)
$imageurl=$artist->images[2]->url;
elseif($artist->images[0]->url)
$imageurl=$artist->images[0]->url;


				DB::table('spotify_items')
					->updateOrInsert(
				['type' => $artist->type,
				 'itemid' => $artist->id],
				[
					'name' => mb_substr($artist->name,0, 500,'UTF-8'),
					'followercount' => $artist->followers->total,
					'genres' => implode(', ', $artist->genres),
					'popularity' => $artist->popularity,
					'imageurl' => $imageurl,
					'url' => $artist->external_urls->spotify,
				'dt' => Carbon::now()]
						);

						$last_id='';
			$last_id = DB::getPdo()->lastInsertId();

			if($last_id>0)
					{
						DB::table('spotify_items')
						->where('id', '=', $last_id)
						->update(['timestamp' => Carbon::now()->timestamp]);


					}


					$updatedOrInsertedRecord2='';

					$updatedOrInsertedRecord2 = DB::table('spotify_items')
					   ->where('type', '=', $artist->type)
					   ->where('itemid', '=', $artist->id)
					   ->first();
						
					   $item_id=$updatedOrInsertedRecord2->id;

					   foreach ($artist->genres as $keyword_id) 
					   {
					   DB::table('spotify_itemkeyword_fk')
						  ->updateOrInsert(
					  ['item_id' => $item_id,
					   'keyword_id' => $keyword_id],
							  );
						}

	SpotifyHelper::instance()->getSingleArtistClaimState($artist->id);				
	
	//delete current if another exists and not equal to the added one
	if($result_check[0]->artistid!=$artist->id && $result_check[0]->spid!='')
	{

		DB::table('spotify_accounts_auth_realartists')
		->where('spid', '=', $result_check[0]->artistid)
		->delete();

	}


	
	DB::table('spotify_accounts_auth_realartists')
					->updateOrInsert(
				['spid' => $artist->id],
				['dt' => Carbon::now(),
				'userid'=>$userid,
				'managerid'=>$result_check[0]->id]
						);

						$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_accounts_auth_realartists')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}




	$row_get2 = DB::table('spotify_accounts_auth_realartists')
	->where('spid', '=', $artist->id)
	->limit(1)
	->get();

	foreach ($row_get2 as $row2) {
		$row_results[]=$row2;
	}


		DB::table('spotify_accounts_auth')
	->where('id', '=', $itemid)
	->where('userid', '=', $userid)
	->update(['artistconnectid' => $row_results[0]->id]);



		return response()->json(array('status'=>"success"), 200);
	}


	public function check_similars(Request $request) {
		
		$userid=Auth::id();
		
		$videorealid = urldecode($request->input('videoid'));

		$currentpage = urldecode($request->input('page'));

		$titlecheck = $request->input('titlecheck');

		$apikey = config('myconfig.youtube.youtube_api_key');
		$resultsperpage = "20";
		
		$videolist = file_get_contents("https://www.googleapis.com/youtube/v3/search?q=".urlencode($titlecheck)."&type=video&maxResults=".$resultsperpage."&key=".$apikey."&fields=items(id)&part=snippet");
	
if ($videolist) {
$json = json_decode($videolist, true);
	
		if (!empty($json['items'])) {
			
			foreach ($json['items'] as $videoResult) { //singlevideos
			$videoid = $videoResult['id']['videoId'];
			
			
		$singlevideos = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$videoid."&key=".$apikey."&fields=items(id,snippet(publishedAt,channelTitle,title,channelId),contentDetails(duration,licensedContent),statistics(viewCount),status(uploadStatus))&part=snippet,contentDetails,statistics,status");
		  
		if ($singlevideos) {
		$json2 = json_decode($singlevideos, true);
		
		
			if($json2['items'][0]['status']['uploadStatus']!='rejected' || $json2['items'][0]['status']['uploadStatus']!='deleted')
			{
				
				$response['videopublishdate'] = date("Y-m-d H:i:s", strtotime($json2['items'][0]['snippet']['publishedAt']));
				
				$response['videopublishago'] = Helperfunctions::instance()->humanTiming(strtotime($json2['items'][0]['snippet']['publishedAt'])).' ago';
				
				$response['videoid']= $videoid;
				
				$response['videotitle']= $json2['items'][0]['snippet']['title'];
		
				$response['videochanneltitle'] = $json2['items'][0]['snippet']['channelTitle'];
				
				$licensedContent = $json2['items'][0]['contentDetails']['licensedContent']; //true=money
				if($licensedContent=='true')
				{
				$response['monetize'] = '1';	
				}
				else
				{
				$response['monetize'] = '0';
				}
				$response['viewcount'] = number_format($json2['items'][0]['statistics']['viewCount']);
		
				$response['videourl'] = "https://www.youtube.com/watch?v=".$videoid;
				
				
				$response['channelurl'] = "https://www.youtube.com/channel/".$json2['items'][0]['snippet']['channelId'];
				
				$duration = new \DateTime('1970-01-01');
				$duration->add(new \DateInterval($json2['items'][0]['contentDetails']['duration']));
				if($duration->format('H')>'0')
				{
				$response['duration'] = $duration->format('H:i:s');
				}
				else
				{
				$response['duration'] = $duration->format('i:s');
				}
				$response['imageurl'] = "https://i.ytimg.com/vi/".$videoid."/mqdefault.jpg";
				
				$allsimilarvideos[]=$response;
				
			}
		
		
		
		}
			
		
				if($currentpage='admin')
						{
							
							DB::table('seencheck')
							->updateOrInsert(
						['userid' => $userid, 'videoid' => $videorealid],
						['seen' => '1']
					);
							
						}
			
			}
	
		
		return response()->json(array('message'=>"good",'allsimilarvideos'=>$allsimilarvideos), 200);
		
			}
	
		}
		
	}
	
}

