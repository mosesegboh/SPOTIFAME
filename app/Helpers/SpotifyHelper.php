<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Helpers\AppHelper as Helperfunctions;
use \Auth;

use Carbon\Carbon;

use GuzzleHttp\Client;

use Session;
use Illuminate\Support\Facades\Crypt;

use App\Helpers\UserAgentHelper;

class SpotifyHelper
{
    private $spotifylogingooglekey;
    private $spotifygetloginurl;
    private $spotifypostloginurl;
    private $spotifycontinueurl;
    private $spotifymainurl;
    private $correction;

	public function __construct()
    {
		$this->spotifylogingooglekey='6Lfdz4QUAAAAABK1wbAdKww1AEvuJuCTVHoWvX8S';

        $this->spotifygetloginurl = "https://accounts.spotify.com/en/login"; 

        $this->spotifypostloginurl = "https://accounts.spotify.com/login/password"; 

        $this->spotifycontinueurl = "https://accounts.spotify.com/en/status"; 

        $this->spotifymainurl = "accounts.spotify.com";

		$this->authuri='https://accounts.spotify.com/en/authorize';

		$this->rediruri='https://spotifame.com/grantspotifyaccess';
		
		$this->postauthuri='https://accounts.spotify.com/authorize/accept';

		$this->correction=100;
	
	}
		
	public function formatDurations($searchresults)
	{

		$s_count=0;
					foreach ($searchresults as $searchresults_s)
					{
						
						if($searchresults[$s_count]->duration_ms!='' && $searchresults[$s_count]->duration_ms>0)
						$searchresults[$s_count]->duration=Helperfunctions::instance()->formatTime($searchresults[$s_count]->duration_ms,true);
						else
						$searchresults[$s_count]->duration=Helperfunctions::instance()->formatTime(0,true);

					$s_count++;
					}

					return $searchresults;
	}

	public function grantAccessToEverything($code='')
	{


		$userid = Auth::id();


		$session = new \SpotifyWebAPI\Session(
			config('myconfig.spotify.clientid'),
			config('myconfig.spotify.secret'),
			config('myconfig.config.server_url').'admin/addspotifyaccount'
		);


		$api = new \SpotifyWebAPI\SpotifyWebAPI();

			if (isset($code) && $code!='') {
				$session->requestAccessToken($code);
				
				$accesstoken=$session->getAccessToken();
				$refreshtoken=$session->getRefreshToken();
				$api->setAccessToken($accesstoken);

				
				 $meobject=$api->me();

				 $displayname=$meobject->display_name;
				 $email=$meobject->email;
				 $url=$meobject->external_urls->spotify;
				 $spid=$meobject->id;

				 $image='';
				 if($meobject->images[2]->url!='')
				 $image=$meobject->images[2]->url;

				 $row_get = DB::table('spotify_accounts_auth')
				 ->where('spid', '=', $spid)
				 ->limit(1)
				 ->get();

				 foreach ($row_get as $row) {
					 $row_results[]=$row;
				 }

				 if(!empty($row_results))
				 {

					DB::table('spotify_accounts_auth')
					->updateOrInsert(
				['spid' => $spid],
				['displayname' => mb_substr($displayname,0, 500,'UTF-8'),
				'email' => $email,
				'url' => $url,
				'image' => $image,
				'accesstoken' => $accesstoken,
				'refreshtoken' => $refreshtoken,
				'dt' => Carbon::now(),
				'tokentimestamp' => Carbon::now()->timestamp,
				'userid'=>$userid]
						);

						$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_accounts_auth')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}


						
					header('Location: ' . config('myconfig.config.server_url').'admin/spotifyaccounts?msg=alreadyaddedbutupdating');
					exit;

				 }
				 else
				 {


				 DB::table('spotify_accounts_auth')
						->updateOrInsert(
					['spid' => $spid],
					['displayname' => mb_substr($displayname,0, 500,'UTF-8'),
					'email' => $email,
					'url' => $url,
					'image' => $image,
					'accesstoken' => $accesstoken,
					'refreshtoken' => $refreshtoken,
					'dt' => Carbon::now(),
					'tokentimestamp' => Carbon::now()->timestamp,
					'userid'=>$userid]
							);

							$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_accounts_auth')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}

				
					header('Location: ' . config('myconfig.config.server_url').'admin/spotifyaccounts?msg=successfullyadded');
					exit;
				 }


			} else {


						$options = [
							'scope' => [
						'user-read-email',
						'user-read-private',
						'ugc-image-upload',
						'playlist-read-collaborative',
						'playlist-modify-public',
						'playlist-read-private',
						'playlist-modify-private',
						'user-library-modify',
						'user-library-read',
						'user-follow-read',
						'user-follow-modify'
						],
					];

				header('Location: ' . $session->getAuthorizeUrl($options));
				exit;
			}


	}


	public function grantAccessForArtistpicks($code='',$hash='')
	{




		$session = new \SpotifyWebAPI\Session(
			config('myconfig.spotify.clientid'),
			config('myconfig.spotify.secret'),
			config('myconfig.config.server_url').'grantspotifyaccess'
		);


		$api = new \SpotifyWebAPI\SpotifyWebAPI();

			if (isset($code) && $code!='') {
				$session->requestAccessToken($code);
				
				$accesstoken=$session->getAccessToken();
				$refreshtoken=$session->getRefreshToken();
				$api->setAccessToken($accesstoken);

				
				 $meobject=$api->me();

				 $spid=$meobject->id;
				 
				 $hash=Session::get('artistclaimhash');

 				//artistcheck
			 
				 $spotifyapi=$this->getSpotifyTokens();
				try {
					
					$artist=$spotifyapi->getArtist($spid);
					
				} catch (\Exception $e) {

					header('Location: ' . config('myconfig.config.server_url').'connectspotify?hash='.$hash.'&msg=notanartist');
										exit;

				}

				 //artistcheck

				 

						$row_get = DB::table('spotify_accounts_auth')
						->where('generatedstr', '=', $hash)
						->limit(1)
						->get();
					
						Session::put('artistclaimhash', '');
						Session::Save();

						foreach ($row_get as $row) {
							$result_check[] = $row;
						}
					
						if(!empty($result_check))
						{
						
							if($spid==$result_check[0]->spid)
							{
							header('Location: ' . config('myconfig.config.server_url').'connectspotify?hash='.$hash.'&msg=cantaddown');
							exit;
							}
							else
							{
								

								$managerid=$result_check[0]->id;
								$userid=$result_check[0]->userid;
							}
						}
						else
						{
							header('Location: ' . config('myconfig.config.server_url').'connectspotify?hash='.$hash.'&msg=hashnotcorrect');
							exit;
						}
						

				 $image='';
				 if($meobject->images[2]->url!='')
				 $image=$meobject->images[2]->url;

				 $row_get = DB::table('spotify_accounts_auth_realartists')
				 ->where('spid', '=', $spid)
				 ->limit(1)
				 ->get();

				 foreach ($row_get as $row) {
					 $row_results[]=$row;
				 }

				 if(!empty($row_results))
				 {
					 

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
	

						$this->getSingleArtistClaimState($artist->id);
					 
					DB::table('spotify_accounts_auth_realartists')
					->updateOrInsert(
				['spid' => $spid],
				['userid'=>$userid,
				'managerid'=>$managerid]
						);



							DB::table('spotify_accounts_auth')
						->where('id', '=', $managerid)
						->update([
							'generatedstr'=>'',
							'artistconnectid' => $row_results[0]->id
							]);
						
					header('Location: ' . config('myconfig.config.server_url').'connectspotify?msg=alreadyaddedbutupdating');
					exit;

				 }
				 else
				 {

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

						$this->getSingleArtistClaimState($artist->id);


						DB::table('spotify_accounts_auth_realartists')
						->updateOrInsert(
					['spid' => $spid],
					['dt' => Carbon::now(),
					'userid'=>$userid,
					'managerid'=>$managerid]
							);

							$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_accounts_auth_realartists')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}




				$row_get = DB::table('spotify_accounts_auth_realartists')
				 ->where('spid', '=', $spid)
				 ->limit(1)
				 ->get();

				 foreach ($row_get as $row) {
					 $row_results[]=$row;
				 }

								DB::table('spotify_accounts_auth')
						->where('id', '=', $managerid)
						->update([
							'generatedstr'=>'',
							'artistconnectid' => $row_results[0]->id
							]);

				
					header('Location: ' . config('myconfig.config.server_url').'connectspotify?msg=successfullyadded');
					exit;
				 }


			} else {


				$options = [
					'scope' => [
						'streaming',
						'ugc-image-upload',
						'user-read-email',
						'user-read-private',
					],
				];

				
				Session::put('artistclaimhash', $hash);
				Session::Save();
				
				
				header('Location: ' . $session->getAuthorizeUrl($options));
				exit;
			}


	}

	public function grantAccessFromOutside($code='',$hash='')
	{


		$session = new \SpotifyWebAPI\Session(
			config('myconfig.spotify.clientid'),
			config('myconfig.spotify.secret'),
			config('myconfig.config.server_url').'grantspotifyaccess'
		);


		$api = new \SpotifyWebAPI\SpotifyWebAPI();

			if (isset($code) && $code!='') {
				$session->requestAccessToken($code);
				
				$accesstoken=$session->getAccessToken();
				$refreshtoken=$session->getRefreshToken();
				$api->setAccessToken($accesstoken);

				
		  $try=true;
          while($try)
          {
            try {
				 $meobject=$api->me();
				 $try=false;
				} catch (\Exception $e) {

					if ($e->getCode() == 429) {
						echo 'Rate limit, trying again:'."\n";
						$responseobject = $meobject->getRequest()->getLastResponse();
									//$responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];
										
										sleep($retryAfter);
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

			//print_r($meobject);


				 $spid=$meobject->id;

				 $displayname=$meobject->display_name;
				 $url=$meobject->external_urls->spotify;
				 $country=$meobject->country;

				 $manageremail=$meobject->email;
				 

					$image='';
				if($meobject->images[2]->url!='')
					$image=$meobject->images[2]->url;

						$row_get = DB::table('spotify_accounts_auth')
						->where('email', '=', $manageremail)
						->limit(1)
						->get();
					

						foreach ($row_get as $row) {
							$result_check[] = $row;
						}
					
						if(!empty($result_check))
						{
						
							$managerid=$result_check[0]->id;
							$userid=$result_check[0]->userid;
							
						}
						else
						{
							return 'problem';
							exit;
						}
						
		   
		   
							DB::table('spotify_accounts_auth')
						->where('id', '=', $managerid)
						->update([
							'spid' => $spid,
							'displayname' => mb_substr($displayname,0, 500,'UTF-8'),
							'url' => $url,
							'image' => $image,
							'country'=>$country,
							'accesstoken' => $accesstoken,
						    'refreshtoken' => $refreshtoken,
							'dt' => Carbon::now(),
							'tokentimestamp' => Carbon::now()->timestamp,
							]);
		   
						
					return 'good';
					exit;

				 
				 


			} 


	}

	public function getSpotifyTokens()
	{

					$row_get = DB::table('spotify_auth')
						->where('codetype', '=', 'access_token')
						->orWhere('codetype', '=', 'refresh_token')
						->limit(2)
						->get();

						$tokens=array();

						foreach ($row_get as $row) {

							$tokens[$row->codetype]=$row;

						}
					
			$expired='0';
			if(Carbon::now()->timestamp-$tokens['access_token']->timestamp>3600)
			{
				$expired='1';
			}

			if($tokens['access_token']->codevalue!='')
			{

				$session = new \SpotifyWebAPI\Session(
					config('myconfig.spotify.clientid'),
					config('myconfig.spotify.secret'),
				);

				if ($expired) //request new
				{

					if($tokens['refresh_token']->codevalue!='')
					{
					// Or request a new access token
					$session->refreshAccessToken($tokens['refresh_token']->codevalue);
					}
					else
					{

						$session = new \SpotifyWebAPI\Session(
							config('myconfig.spotify.clientid'),
							config('myconfig.spotify.secret'),
							config('myconfig.config.server_url').'admin/search'
						);

					}

				}
				else // use it
				{

					$session->setAccessToken($tokens['access_token']->codevalue);
					$session->setRefreshToken($tokens['refresh_token']->codevalue);


				}



			}
			else
			{

				if($tokens['refresh_token']->codevalue!='')
					{
						$session = new \SpotifyWebAPI\Session(
							config('myconfig.spotify.clientid'),
							config('myconfig.spotify.secret'),
						);
					// Or request a new access token
					$session->refreshAccessToken($tokens['refresh_token']->codevalue);
					}
					else
					{

						$session = new \SpotifyWebAPI\Session(
							config('myconfig.spotify.clientid'),
							config('myconfig.spotify.secret'),
							config('myconfig.config.server_url').'admin/search'
						);
						

					}

				
			}

			$options = [
				'auto_refresh' => true,
			];

			$spotifyapi = new \SpotifyWebAPI\SpotifyWebAPI($options, $session);

			// You can also call setSession on an existing SpotifyWebAPI instance
			$spotifyapi->setSession($session);

			// Remember to grab the tokens afterwards, they might have been updated
			$newAccessToken = $session->getAccessToken();
			$newRefreshToken = $session->getRefreshToken(); // Sometimes, a new refresh token will be returned

			if($newAccessToken!=$tokens['access_token']->codevalue 
			|| $newRefreshToken!=$tokens['refresh_token']->codevalue)
			{

				
				DB::table('spotify_auth')
						->updateOrInsert(
					['codetype' => 'access_token'],
					['codevalue' => $newAccessToken,'dt' => Carbon::now(),'timestamp' => Carbon::now()->timestamp]
							);
					

						

				DB::table('spotify_auth')
						->updateOrInsert(
					['codetype' => 'refresh_token'],
					['codevalue' => $newRefreshToken,'dt' => Carbon::now(),'timestamp' => Carbon::now()->timestamp]
							);
							

	 

			}


			return $spotifyapi;

	}

	public function getSpotifySearchTokens()
	{

					$row_get = DB::table('spotify_auth')
						->where('codetype', '=', 'access_token_average')
						->orWhere('codetype', '=', 'refresh_token_average')
						->limit(2)
						->get();

						$tokens=array();

						foreach ($row_get as $row) {

							$tokens[$row->codetype]=$row;

						}
						$expired='0';
						if(Carbon::now()->timestamp-$tokens['access_token_average']->timestamp>3600)
						{
							$expired='1';
						}
			
			
						if($tokens['access_token_average']->codevalue!='')
						{
			
							$session = new \SpotifyWebAPI\Session(
								config('myconfig.spotify.clientid'),
								config('myconfig.spotify.secret'),
							);
			
							if ($expired) //request new
							{
			
								if($tokens['refresh_token_average']->codevalue!='')
								{
								// Or request a new access token
									
									$session->refreshAccessToken($tokens['refresh_token_average']->codevalue);
									
			
								}
								else
								{
			
									// no refresh token found
							return response()->json(array('status'=>"failed",'msg'=>'No refresh token found. Sorry you have to add your spotify account to our website.'), 200);
							exit;
									// no refresh token found
			
								}
			
							}
							else // use it
							{
			
								$session->setAccessToken($tokens['access_token_average']->codevalue);
								$session->setRefreshToken($tokens['refresh_token_average']->codevalue);
			
			
							}
			
			
			
						}
						else
						{
			
							if($tokens['refresh_token']!='')
								{
									$session = new \SpotifyWebAPI\Session(
										config('myconfig.spotify.clientid'),
										config('myconfig.spotify.secret'),
									);
								// Or request a new access token
								
									$session->refreshAccessToken($tokens['refresh_token_average']->codevalue);
									
								}
								else
								{
			
									// no access or refresh tokens found
							return response()->json(array('status'=>"failed",'msg'=>'No access or refresh token found. Sorry you have to add your spotify account to our website.'), 200);
							exit;
									// no access or refresh tokens found
									
			
								}
			
							
						}
			
						$options = [
							'auto_refresh' => true,
						];
			
						$spotifyapi = new \SpotifyWebAPI\SpotifyWebAPI($options, $session);
			
						// You can also call setSession on an existing SpotifyWebAPI instance
						$spotifyapi->setSession($session);
			
						// Remember to grab the tokens afterwards, they might have been updated
						$newAccessToken = $session->getAccessToken();
						$newRefreshToken = $session->getRefreshToken(); // Sometimes, a new refresh token will be returned
			
						if($newAccessToken!=$tokens['access_token_average']->codevalue
						|| $newRefreshToken!=$tokens['refresh_token_average']->codevalue)
						{
			
							DB::table('spotify_auth')
						->updateOrInsert(
					['codetype' => 'access_token_average'],
					['codevalue' => $newAccessToken,'dt' => Carbon::now(),'timestamp' => Carbon::now()->timestamp]
							);
					

						

				DB::table('spotify_auth')
						->updateOrInsert(
					['codetype' => 'refresh_token_average'],
					['codevalue' => $newRefreshToken,'dt' => Carbon::now(),'timestamp' => Carbon::now()->timestamp]
							);


										
								
			
						}
			
			
			
			
						return $spotifyapi;
			
			

	}


	public function getMySpotifyTokens($managerid)
	{
	

		$row_get = DB::table('spotify_accounts_auth')
						->where('id', '=', $managerid)
						->limit(1)
						->get();

						$tokens=array();

						foreach ($row_get as $row) {
							$tokens['tokentimestamp']=$row->tokentimestamp;
							$tokens['access_token']=$row->accesstoken;
							$tokens['refresh_token']=$row->refreshtoken;

							$userid=$row->userid;
						}

					
			$expired='0';
			if(Carbon::now()->timestamp-$tokens['tokentimestamp']>3600)
			{
				$expired='1';
			}

			$problemlogs='';

			if($tokens['access_token']!='')
			{

				$session = new \SpotifyWebAPI\Session(
					config('myconfig.spotify.clientid'),
					config('myconfig.spotify.secret'),
				);

				if ($expired) //request new
				{

					if($tokens['refresh_token']!='')
					{
					// Or request a new access token
						$try=true;
						while($try)
						{
							try {
							$session->refreshAccessToken($tokens['refresh_token']);
							$try=false;
							} catch (\Exception $e) {

								if ($e->getCode() == 429) { //rate limit, so try again
									
								}
								else
								{

								$problemlogs.='Caught exception: '.$e->getMessage()."\n";
								$problemlogs.='Current problematic manager account: '.$row->id."\n\n";
								$try=false;
								}
								
							}
						}

					}
					else
					{

						// no refresh token found
				return response()->json(array('status'=>"failed",'msg'=>'No refresh token found. Sorry you have to add your spotify account to our website.'), 200);
				exit;
						// no refresh token found

					}

				}
				else // use it
				{

					$session->setAccessToken($tokens['access_token']);
					$session->setRefreshToken($tokens['refresh_token']);


				}



			}
			else
			{

				if($tokens['refresh_token']!='')
					{
						$session = new \SpotifyWebAPI\Session(
							config('myconfig.spotify.clientid'),
							config('myconfig.spotify.secret'),
						);
					// Or request a new access token
					
					$try=true;
						while($try)
						{
							try {
								$session->refreshAccessToken($tokens['refresh_token']);
								$try=false;
								} catch (\Exception $e) {

									if ($e->getCode() == 429) { //rate limit, so try again
									
									}
									else
									{

									$problemlogs.='Caught exception: '.$e->getMessage()."\n";
									$problemlogs.='Current problematic manager account: '.$row->id."\n\n";
									$try=false;
									}
									
								}
						}
					}
					else
					{

						// no access or refresh tokens found
				return response()->json(array('status'=>"failed",'msg'=>'No access or refresh token found. Sorry you have to add your spotify account to our website.'), 200);
				exit;
						// no access or refresh tokens found
						

					}

				
			}

			$options = [
				'auto_refresh' => true,
			];

			$spotifyapi = new \SpotifyWebAPI\SpotifyWebAPI($options, $session);

			// You can also call setSession on an existing SpotifyWebAPI instance
			$spotifyapi->setSession($session);

			// Remember to grab the tokens afterwards, they might have been updated
			$newAccessToken = $session->getAccessToken();
			$newRefreshToken = $session->getRefreshToken(); // Sometimes, a new refresh token will be returned

			if($newAccessToken!=$tokens['access_token']
			|| $newRefreshToken!=$tokens['refresh_token'])
			{

				DB::table('spotify_accounts_auth')
						->updateOrInsert(
					['id' => $managerid],
					['accesstoken' => $newAccessToken,
					'refreshtoken' => $newRefreshToken,
					'tokentimestamp' => Carbon::now()->timestamp]
							);
					

			}



			if($problemlogs!='')
			{
			DB::table('spotify_accounts_logs')
						->insert(
					['userid' => $userid,
					  'type'=>'tokens'],
					['thecontent' => $problemlogs,
					'dt' => Carbon::now(),
					'timestamp' => Carbon::now()->timestamp]
							);

					return 'wrong';
			}



			return $spotifyapi;





	}

	public function getSpotifyItemId($link)
	{
		/* variations
		*	spotify:user:9fsyz2t3gffvgjczbh5xqkie4:playlist:6Gv0h0g2UZ2NAfBlBeZTR9
		*	spotify:album:3xIwVbGJuAcovYIhzbLO3J
		*	spotify:track:5ChkMS8OtdzJeqyybCc9R5
		*	spotify:show:14GmS2z02cRmvndNmYlDwo
		*	spotify:episode:5bSnc7rjo1BY6Mx6yx7dbj
		*	
		*	https://open.spotify.com/playlist/37i9dQZF1DXcBWIGoYBM5M?si=enRCpF5_QGOsHh9ygeQS1g
		*	https://open.spotify.com/album/1C2h7mLntPSeVYciMRTF4a
		*	https://open.spotify.com/track/4HBZA5flZLE435QTztThqH?si=enRCpF5_QGOsHh9ygeQS1g
		*	https://open.spotify.com/show/5pD4QqTtrGyXxJviHU7vDx
		*	https://open.spotify.com/episode/2p2teEw5mcVM2VisyM2rAo
		*/

		$returnarray=array(
			'id'=>'',
			'type'=>''
		);
		$regexes=array();


		$regexes=array(
			'playlist'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/playlist\/|spotify:(?:user:[a-zA-Z0-9]+:)?playlist:)([a-zA-Z0-9]+)(.*)$/',
			'album'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/album\/|spotify:(?:user:)?album:)([a-zA-Z0-9]+)(.*)$/',
			'track'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/track\/|spotify:(?:user:)?track:)([a-zA-Z0-9]+)(.*)$/',
			'show'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/show\/|spotify:(?:user:)?show:)([a-zA-Z0-9]+)(.*)$/',
			'episode'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/episode\/|spotify:(?:user:)?episode:)([a-zA-Z0-9]+)(.*)$/'
		);
		


			foreach($regexes as $key => $regex_single)
			{

				if(preg_match($regex_single, $link, $matches))
				{
					$returnarray['id']=$matches[2];
					$returnarray['type']=$key;
					return $returnarray;
					break;
				}

			}


		return $returnarray;

	}

	public function getAudioFeatures($spotifyapi,$trackid)
	{
		$try=true;
			while($try)
			{
				try {
					
						$response = $spotifyapi->getAudioFeatures($trackid);

					$try=false;
					
				} catch (\Exception $e) {

						if ($e->getCode() == 429) {

							$responseobject = $spotifyapi->getRequest()->getLastResponse();
							$responsestatus=$responseobject['status'];
							$retryAfter = $responseobject['headers']['Retry-After'];

							
							sleep($retryAfter);
							
						}
						else
						{
							
							$try=false;
						}
						
				}
			}

			return $response;
	}

	public function getAudioAnalysis($spotifyapi,$trackid)
	{
		$try=true;
			while($try)
			{
				try {
					
						$response = $spotifyapi->getAudioAnalysis($trackid);

					$try=false;
					
				} catch (\Exception $e) {

						if ($e->getCode() == 429) {

							$responseobject = $spotifyapi->getRequest()->getLastResponse();
							$responsestatus=$responseobject['status'];
							$retryAfter = $responseobject['headers']['Retry-After'];

							
							sleep($retryAfter);
							
						}
						else
						{
							
							$try=false;
						}
						
				}
			}

			return $response;
	}

	public function getPlaylistTracks($spotifyapi,$playlistid,$fields='total,items(track(id))',$market='',$perpage=10)
	{
		
			$alltracks=array();
		

		$item_count=0;
		$options=new \stdClass();
		$options->limit=$perpage;
		$options->offset=0;
		$options->fields=$fields;
		if($market!='')
		$options->market=$market;
		
        
           $try=true;
           while($try)
           {
               try {
                   $firstresultobject=$spotifyapi->getPlaylistTracks($playlistid,$options);
   
                   $try=false;
               } catch (\Exception $e) {
   
                       if ($e->getCode() == 429) {
   
                           
                           
                       }
                       else
                       {
                           
                           $try=false;
                       }
                       
               }
           }

		$item_count=$firstresultobject->total;

		if($item_count==0)
        {
           echo 'no tracks found';
            return $alltracks;
        }

		$curoffset=(int) 0;
		
			$try=true;
			while($curoffset<=$item_count && $try)
			{

				$options=new \stdClass();
				$options->limit=$perpage;
				$options->offset=$curoffset;
				$options->fields=$fields;
				if($market!='')
				$options->market=$market;

						$try2=true;
						$tracks=array();
						while($try2)
						{
							try {

								$tracks=$spotifyapi->getPlaylistTracks($playlistid,$options)->items;
								
                                $try2=false;
                            } catch (\Exception $e) {

							
									
                                if ($e->getCode() == 429) {
                                    
                                   $responseobject = $spotifyapi->getRequest()->getLastResponse();
                                    $responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];

									sleep($retryAfter);

                                }
                                else
                                {
									echo $e->getCode();
									$try2=false;
                                }



							}
							
						}


						
					
				$curoffset+=$perpage;

				if(empty($tracks))
				$try=false;
				

				foreach ($tracks as $track)
					{
						array_push($alltracks,$track);
					}
			}



			return $alltracks;
	}

	public function checkSpotifyItemId($link,$managerid)
	{
		/* check if it exists on Spotify
		*/
		$returnarray=array(
			'id'=>'',
			'type'=>''
		);


		$myspotifyapi=$this->getMySpotifyTokens($managerid);

		//getPlaylist($playlistId, $options)
		//getAlbum($albumId, $options)
		//getTrack($trackId, $options)
		//getShow($showId, $options)
		//getEpisode($episodeId, $options)

		$typearray=array('playlist','album','track','show','episode');

		foreach ($typearray as $type)
					{


						$try=true;
						while($try)
						{
							try {
								
								if($type=='playlist')
								$theresult=$myspotifyapi->getPlaylist($link);
								elseif($type=='album')
								$theresult=$myspotifyapi->getAlbum($link);
								elseif($type=='track')
								$theresult=$myspotifyapi->getTrack($link);
								elseif($type=='show')
								$theresult=$myspotifyapi->getShow($link);
								elseif($type=='episode')
								$theresult=$myspotifyapi->getEpisode($link);

				
							} catch (\Exception $e) {
								
				
									if ($e->getCode() == 429) {
										$responseobject = $myspotifyapi->getRequest()->getLastResponse();
									//$responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];

										sleep($retryAfter);
									}
									else
									{
										$try=false;
									}

							}

							if($theresult->name!='')
							{
								$returnarray['id']=$link;
								$returnarray['type']=$type;

								$try=false;
								break 2;

							}


						}


					}

		
			return $returnarray;
		


	}

	public function getSpotifyItem($spotifyapi,$itemid,$itemtype)
	{
		
		$response=array();


		
						$try=true;
						while($try)
						{
							try {
								
								if($itemtype=='playlist')
								$response=$spotifyapi->getPlaylist($itemid);
								elseif($itemtype=='album')
								$response=$spotifyapi->getAlbum($itemid);
								elseif($itemtype=='track')
								$response=$spotifyapi->getTrack($itemid);
								elseif($itemtype=='show')
								$response=$spotifyapi->getShow($itemid);
								elseif($itemtype=='episode')
								$response=$spotifyapi->getEpisode($itemid);
								elseif($itemtype=='artist')
								$response=$spotifyapi->getArtist($itemid);
								
								$try=false;
				
							} catch (\Exception $e) {
								
				
									if ($e->getCode() == 429) {
										$responseobject = $spotifyapi->getRequest()->getLastResponse();
									//$responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];

										sleep($retryAfter);
									}
									else
									{
										$try=false;
									}

							}


						}

		
			return $response;
		


	}


	public function getSpotifyArtistItemId($link)
	{
		/* variations
		*	spotify:artist:3fMbdgg4jU18AjLCKBhRSm
		*
		*	https://open.spotify.com/artist/3fMbdgg4jU18AjLCKBhRSm
		*/

		$returnarray=array(
			'id'=>'',
			'type'=>''
		);
		$regexes=array();


		$regexes=array(
			'artist'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/artist\/|spotify:(?:user:)?artist:)([a-zA-Z0-9]+)(.*)$/',
		);
		


			foreach($regexes as $key => $regex_single)
			{

				if(preg_match($regex_single, $link, $matches))
				{
					$returnarray['id']=$matches[2];
					$returnarray['type']=$key;
					return $returnarray;
					break;
				}

			}


		return $returnarray;

	}

	public function getSpotifyPlaylistItemId($link)
	{
		/* variations
		*	spotify:user:9fsyz2t3gffvgjczbh5xqkie4:playlist:6Gv0h0g2UZ2NAfBlBeZTR9
		*	
		*	https://open.spotify.com/playlist/37i9dQZF1DXcBWIGoYBM5M?si=enRCpF5_QGOsHh9ygeQS1g
		*/

		$returnarray=array(
			'id'=>'',
			'type'=>''
		);
		$regexes=array();


		$regexes=array(
			'playlist'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/playlist\/|spotify:(?:user:[a-zA-Z0-9]+:)?playlist:)([a-zA-Z0-9]+)(.*)$/',
		);
		


			foreach($regexes as $key => $regex_single)
			{

				if(preg_match($regex_single, $link, $matches))
				{
					$returnarray['id']=$matches[2];
					$returnarray['type']=$key;
					return $returnarray;
					break;
				}

			}


		return $returnarray;

	}

	public function getSpotifyTrackItemId($link)
	{
		/* variations
		*	spotify:track:5ChkMS8OtdzJeqyybCc9R5
		*
		*	https://open.spotify.com/track/3fMbdgg4jU18AjLCKBhRSm
		*/

		$returnarray=array(
		);
		
		$regexes=array();


		$regexes=array(
			'track'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/track\/|spotify:(?:user:)?track:)([a-zA-Z0-9]+)(.*)$/',
		);
		


			foreach($regexes as $key => $regex_single)
			{

				if(preg_match($regex_single, $link, $matches))
				{
					$returnarray['id']=$matches[2];
					$returnarray['type']=$key;
					return $returnarray;
					break;
				}

			}


		return $returnarray;

	}

	public function getSpotifyAlbumItemId($link)
	{
		/* variations
		*	spotify:album:3xIwVbGJuAcovYIhzbLO3J
		*
		*	https://open.spotify.com/album/3fMbdgg4jU18AjLCKBhRSm
		*/

		$returnarray=array(
		);
		
		$regexes=array();


		$regexes=array(
			'album'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/album\/|spotify:(?:user:)?album:)([a-zA-Z0-9]+)(.*)$/',
		);
		


			foreach($regexes as $key => $regex_single)
			{

				if(preg_match($regex_single, $link, $matches))
				{
					$returnarray['id']=$matches[2];
					$returnarray['type']=$key;
					return $returnarray;
					break;
				}

			}


		return $returnarray;

	}
	
	public function getSpotifyUserItemId($link)
	{
		/* variations
		*	spotify:user:3xIwVbGJuAcovYIhzbLO3J
		*
		*	https://open.spotify.com/user/3fMbdgg4jU18AjLCKBhRSm
		*/

		$returnarray=array(
		);
		
		$regexes=array();


		$regexes=array(
			'user'=>'/^((?:https?:\/\/)?(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/user\/|spotify:(?:user:)?user:)([a-zA-Z0-9]+)(.*)$/',
		);
		


			foreach($regexes as $key => $regex_single)
			{

				if(preg_match($regex_single, $link, $matches))
				{
					$returnarray['id']=$matches[2];
					$returnarray['type']=$key;
					return $returnarray;
					break;
				}

			}


		return $returnarray;

	}


	public function getAllArtistAlbums($spotifyapi,$artistid,$perpage=10)
	{

		$allalbums=array();
		
		$maximumalbums=200;

		$item_count=0;
		$options=new \stdClass();
		$options->limit=$perpage;
		$options->offset=0;
		$options->include_groups='single,album';
		
        
           $try=true;
           while($try)
           {
               try {
                   $firstresultobject=$spotifyapi->getArtistAlbums($artistid,$options);
   
                   $try=false;
               } catch (\Exception $e) {
   
                       if ($e->getCode() == 429) {
   
                           
                           
                       }
                       else
                       {
                           
                           $try=false;
                       }
                       
               }
           }

		$item_count=$firstresultobject->total;

		if($item_count==0)
        {
           echo 'no albums found';
            return $allalbums;
        }

		$curoffset=(int) 0;
		
			$try=true;
			while($curoffset<=$item_count && $curoffset<=$maximumalbums && $try)
			{

				$options=new \stdClass();
					$options->limit=$perpage;
					$options->offset=$curoffset;
					$options->include_groups='single,album';

						$try2=true;
						$albums=array();
						while($try2)
						{
							try {

								$albums=$spotifyapi->getArtistAlbums($artistid,$options)->items;
								
                                $try2=false;
                            } catch (\Exception $e) {

							
									
                                if ($e->getCode() == 429) {
                                    
                                   $responseobject = $spotifyapi->getRequest()->getLastResponse();
                                    $responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];

									sleep($retryAfter);

                                }
                                else
                                {
									echo $e->getCode();
									$try2=false;
                                }



							}
							
						}


						
					
				$curoffset+=$perpage;

				if(empty($albums))
				$try=false;
				

				foreach ($albums as $album)
					{
						array_push($allalbums,$album);
					}
			}



			return $allalbums;
						

	}

	public function getAllAlbumTracks($spotifyapi,$managerid,$albumid,$perpage=10)
	{

		$alltracks=array();
		

		$item_count=0;
		$options=new \stdClass();
		$options->limit=$perpage;
		$options->offset=0;
		
        
           $try=true;
           while($try)
           {
               try {
                   $firstresultobject=$spotifyapi->getAlbumTracks($albumid, $options);
   
                   $try=false;
               } catch (\Exception $e) {
   
                       if ($e->getCode() == 429) {
   
						$responseobject = $spotifyapi->getRequest()->getLastResponse();
						$responsestatus=$responseobject['status'];
						$retryAfter = $responseobject['headers']['Retry-After'];
						sleep($retryAfter);
                       }
                       else
                       {
                           
                           $try=false;
                       }
                       
               }
           }

		$item_count=$firstresultobject->total;

		if($item_count==0)
        {
           echo 'no tracks found';
            return $alltracks;
		}
		
		$curoffset=(int) 0;
		
			$try=true;
			while($curoffset<=$item_count && $try)
			{

				$options=new \stdClass();
					$options->limit=$perpage;
					$options->offset=$curoffset;

				$try2=true;
				$tracks=array();
				while($try2)
				{
					try {
						
						$tracks=$spotifyapi->getAlbumTracks($albumid, $options)->items;
						
						$try2=false;
					} catch (\Exception $e) {

					
						if ($e->getCode() == 429) {
							$responseobject = $spotifyapi->getRequest()->getLastResponse();
							$responsestatus=$responseobject['status'];
							$retryAfter = $responseobject['headers']['Retry-After'];
						
							sleep($retryAfter);


						}
						else
						{
							echo $e->getCode();
							$try2=false;
						}



					}
					
				}


				$curoffset+=$perpage;

				if(empty($tracks))
				$try=false;
				

				foreach ($tracks as $track)
					{
						array_push($alltracks,$track);
					}



			}


		return $alltracks;

	}


	public function replacePlaylistTracks($managerid,$playlistid,$tracks)
	{

		$response=false;
		$myspotifyapi=$this->getMySpotifyTokens($managerid);

				$try=true;
				
				while($try)
				{
					try {
						
						$response=$myspotifyapi->replacePlaylistTracks($playlistid,$tracks);
						
						$try=false;
					} catch (\Exception $e) {

						
							
						if ($e->getCode() == 429) {
							$responseobject = $myspotifyapi->getRequest()->getLastResponse();
							$responsestatus=$responseobject['status'];
							$retryAfter = $responseobject['headers']['Retry-After'];
						

							sleep($retryAfter);

						}
						else
						{
							echo $e->getCode();
							$try=false;
						}



					}
					
				}


		return $response;

	}

	public function addPlaylistTracks($managerid,$playlistid,$tracks)
	{

		$response=false;
		$myspotifyapi=$this->getMySpotifyTokens($managerid);

				$try=true;
				
				while($try)
				{
					try {
						
						$response=$myspotifyapi->addPlaylistTracks($playlistid,$tracks);
						
						$try=false;
					} catch (\Exception $e) {

						
							
						if ($e->getCode() == 429) {
							
							$responseobject = $myspotifyapi->getRequest()->getLastResponse();
							$responsestatus=$responseobject['status'];
							$retryAfter = $responseobject['headers']['Retry-After'];

							sleep($retryAfter);

						}
						else
						{
							echo $e->getCode();
							$try=false;
						}



					}
					
				}


		return $response;

	}


	public function updatePlaylist($managerid,$playlistid,$options=array())
	{
		
		$response=false;
		$myspotifyapi=$this->getMySpotifyTokens($managerid);

				$try=true;
				
				while($try)
				{
					try {
						
						$response=$myspotifyapi->updatePlaylist($playlistid,$options);
						
						$try=false;
					} catch (\Exception $e) {

						
							
						if ($e->getCode() == 429) {
							$responseobject = $myspotifyapi->getRequest()->getLastResponse();
							$responsestatus=$responseobject['status'];
							$retryAfter = $responseobject['headers']['Retry-After'];
						

							sleep($retryAfter);

						}
						else
						{
							echo $e->getCode();
							$try=false;
						}



					}
					
				}


		return $response;

	}
	
	public function deletePlaylistTracks($spotifyapi,$playlistid, $tracks, $snapshotid='')
	{

		$response=false;

				$try=true;
				
				while($try)
				{
					try {
						
						$response=$spotifyapi->deletePlaylistTracks($playlistid, $tracks, $snapshotid);
						
						$try=false;
					} catch (\Exception $e) {

						
							
						if ($e->getCode() == 429) {
							
							$responseobject = $spotifyapi->getRequest()->getLastResponse();
							$responsestatus=$responseobject['status'];
							$retryAfter = $responseobject['headers']['Retry-After'];

							sleep($retryAfter);

						}
						else
						{
							echo $e->getCode();
							$try=false;
						}



					}
					
				}


		return $response;

	}

	public function getArtistsTopTracks($spotifyapi,$artistid,$country='US')
	{

		$artisttoptracks=array();

		$options=new \stdClass();
		$options->country=$country;
		
        
           $try=true;
           while($try)
           {
               try {
				$artisttoptracks=$spotifyapi->getArtistTopTracks($artistid,$options);
   
                   $try=false;
               } catch (\Exception $e) {
   
                       if ($e->getCode() == 429) {
   
                           
                           
                       }
                       else
                       {
                           
                           $try=false;
                       }
                       
               }
           }


		
			return $artisttoptracks;
						

	}

	public function getArtistProfileBearerToken($managerid)
	{


		$gettheresultset=DB::table('spotify_accounts_auth as t1')
                    ->select('t1.*','t3.spid AS artistid','t3.active AS artistactive')
                     ->leftJoin('spotify_accounts_auth_realartists AS t3', function($join)
							{
                            $join->on('t1.artistconnectid', '=', 't3.id');
                            })
                    ->where('t1.userid', '!=', '0')
					->where('t1.id', '=', $managerid)
					->limit(1)
                    ->get();

					foreach($gettheresultset as $gettheresultset_s)
					{

						$gettheresultset_final=$gettheresultset_s;
					}
				

					if($gettheresultset_final->active!='1')
					{
						echo 'usmanager is not active: '.$gettheresultset_final->id;;
						return 'wrong';
						
					}

					if($gettheresultset_final->state!='1')
					{
						echo 'usmanager is not in good state: '.$gettheresultset_final->id;;
						return 'wrong';
						
					}

					if($gettheresultset_final->thingstr=='')
					{
						echo 'manager password not good: '.$gettheresultset_final->id;;
						return 'wrong';
						
					}

					if($gettheresultset_final->artistactive!='1')
					{
						echo 'artist is not active: '.$gettheresultset_final->artistid;
						return 'wrong';

					}


					if($gettheresultset_final->useragent!='')
                                    {
                                        $myUserAgent = $gettheresultset_final->useragent;
                                    }
                                    else
                                    {
                                        $myUserAgent = UserAgentHelper::instance()->generate();

                                        DB::table('spotify_accounts_auth')
                                        ->where('id',$gettheresultset_final->id)
                                        ->update(['useragent' => $myUserAgent]);   
									}


		$bearertoken=$this->checkBearerToken($gettheresultset_final,100,$myUserAgent);

					return $bearertoken;				

	}


	public function checkBearerToken($theresultset_s,$correction,$myUserAgent)
    {
      

        if((Carbon::now()->timestamp-($theresultset_s->bearertimestamp+$correction))>3600 || $theresultset_s->bearertoken=='')
                                        {

                                            if($theresultset_s->cookies!='')
                                            {
                                                echo '...got cookies so getting bearer token...'."\n";
                                                $bearertoken=$this->renewBearerToken($theresultset_s->cookies,$theresultset_s->id,$theresultset_s->email,$theresultset_s->thingstr,$myUserAgent);
                                   
                                            }
                                            else
                                            {
                                                echo '...getting cookies...'."\n";
												$cookies=$this->getSpotifyCookies($theresultset_s->id,$theresultset_s->email,$theresultset_s->thingstr,$myUserAgent);
												
                                                echo '...getting bearer token...'."\n";
                                                $bearertoken=$this->renewBearerToken($cookies,$theresultset_s->id,$theresultset_s->email,$theresultset_s->thingstr,$myUserAgent);
                                               
                                            }


                                        }
                                        else  // artistpickstate=0 and bearer token is good
                                        {
                                            echo '...got bearer token...'."\n";
                                            $bearertoken=$theresultset_s->bearertoken;

                                        }

            return $bearertoken;

    }



	public function renewBearerToken($cookies,$managerid,$manageremail,$managerpass,$myUserAgent)
    {

        $c = curl_init('https://generic.wg.spotify.com/creator-auth-proxy/v1/web/token?client_id=6cf79a93be894c2086b8cbf737e0796b'); 
        curl_setopt($c, CURLOPT_COOKIE, $cookies); 
        curl_setopt($c, CURLOPT_USERAGENT, $myUserAgent);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); 
        $page = curl_exec($c); curl_close($c);

        
        $bearertoken='';
        $bearertoken=json_decode($page)->access_token;

        if($bearertoken=='')
        {
            //problem

            echo 'Problem with getting bearer token for manager, getting new cookies :'.$managerid;
            
            DB::table('spotify_accounts_auth')
            ->where('id',$managerid)
            ->update(['cookies' => '']);
            $this->getSpotifyCookies($managerid,$manageremail,$managerpass,$myUserAgent);

        }



        if($bearertoken=='')
        {
            //problem
                      

            echo 'Problem with getting bearer token still after trying to get new cookies for manager :'.$managerid."\n";
            exit;


        }

        //update bearertoken
        DB::table('spotify_accounts_auth')
                    ->where('id',$managerid)
                    ->update(['bearertoken' => $bearertoken,
                              'bearertimestamp' => Carbon::now()->timestamp]);
                    
        //update bearertoken

        return $bearertoken;
    }


	public function checkArtistItemId($link,$managerid)
	{
		/* check if it exists on Spotify
		*/
		$returnarray=array(
			'id'=>'',
			'type'=>''
		);

		$myspotifyapi=$this->getMySpotifyTokens($managerid);

		//getArtist($artistId)
		

						$try=true;
						while($try)
						{
							try {
								
								$theresult=$myspotifyapi->getArtist($link);
								
							} catch (\Exception $e) {
								
				
									if ($e->getCode() == 429) {
										$responseobject = $myspotifyapi->getRequest()->getLastResponse();
									//$responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];

										sleep($retryAfter);
									}
									else
									{
										$try=false;
									}

							}

							if($theresult->name!='')
							{
								$returnarray['id']=$link;
								$returnarray['type']='artist';

								$try=false;
								
							}


						}



		
			return $returnarray;
		

	}

	public function getArtist($spotifyapi,$artistid)
	{
		

						$try=true;
						while($try)
						{
							try {
								
								$response=$spotifyapi->getArtist($artistid);
								$try=false;
							} catch (\Exception $e) {
								
				
									if ($e->getCode() == 429) {
										$responseobject = $spotifyapi->getRequest()->getLastResponse();
									//$responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];

										sleep($retryAfter);
									}
									else
									{
										$try=false;
									}

							}


						}



		
			return $response;
		

	}

	public function getPlaylist($spotifyapi,$playlistid)
	{
		

						$try=true;
						while($try)
						{
							try {
								
								$response=$spotifyapi->getPlaylist($playlistid);
								$try=false;
							} catch (\Exception $e) {
								
				
									if ($e->getCode() == 429) {
										$responseobject = $spotifyapi->getRequest()->getLastResponse();
									//$responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];

										sleep($retryAfter);
									}
									else
									{
										$try=false;
									}

							}


						}



		
			return $response;
		

	}

	public function transformRangeValue($number,$breakpoint,$maximumvalue)
	{
	


		$breakpointoldvalue=round($maximumvalue/2);

		if($number<$breakpointoldvalue)
		{
			return round(($number*$breakpoint)/$breakpointoldvalue);

		}
		elseif($number>=$breakpointoldvalue){
			return $breakpoint+round((($number - $breakpointoldvalue)*($maximumvalue-$breakpoint)/($maximumvalue - $breakpointoldvalue)));
		}

	}

	public function getExtraInformation($spotifyapi,$type,$searchresults)
	{
		
		if(!in_array($type,array('playlist')))
		return;
		
		$s_count=0;
					foreach ($searchresults as $searchresults_s)
					{

			$itemid=$searchresults_s->id;

						$try=true;
						while($try)
						{
							try {
								
								if($type=='playlist')
								$apisearchobject=$spotifyapi->getPlaylist($itemid);
				
								$try=false;
								
							} catch (\Exception $e) {
				
									
				
				
									if ($e->getCode() == 429) {
										$responseobject = $spotifyapi->getRequest()->getLastResponse();
									//$responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];
										
										sleep($retryAfter);
										
									}
									else
									{
										
										$try=false;
									}
									
							}
						}


						if($type=='playlist')
					{
						$searchresults[$s_count]->followers=new \stdClass();
						$searchresults[$s_count]->followers->total=$apisearchobject->followers->total;
					}

			
					$s_count++;
					}


		return $searchresults;



	}


	public function getSingleFollowerCount($spotifyapi,$type,$itemid)
	{
		
		if(!in_array($type,array('playlist')))
		return;
		
						$try=true;
						while($try)
						{
							try {
								
								if($type=='playlist')
								$apisearchobject=$spotifyapi->getPlaylist($itemid);
				
								$try=false;
								
							} catch (\Exception $e) {
				
									if ($e->getCode() == 429) {
				
										$responseobject = $spotifyapi->getRequest()->getLastResponse();
									//$responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];
										
										sleep($retryAfter);
										
									}
									else
									{
										
										$try=false;
									}
									
							}
						}


				if($type=='playlist')
					{
			return $apisearchobject->followers->total;
					}


	}


	public function getArtistClaimState($searchresults,$hideclaimed,$user_bearer_token)
	{

							$s_count=0;
					foreach ($searchresults as $searchresults_s)
					{

						
						$artisttoken=$searchresults_s->id;

						$row_artists_get = DB::table('spotify_items')
					->where('type', '=', 'artist')
					->where('itemid', '=', $artisttoken)
					->limit(1)
					->get();

					$row_artists=array();
					foreach ($row_artists_get as $row) {
						$row_artists[] = $row;
					}


					if($row_artists[0]->claimed=='1' || $row_artists[0]->claimed=='3')
						{
							
							$searchresults[$s_count]->claimed=$row_artists[0]->claimed;
							$searchresults[$s_count]->distributorname=$row_artists[0]->distributorname;
							$searchresults[$s_count]->distributorurl=$row_artists[0]->distributorurl;

					
					if($hideclaimed=='on' && ($searchresults[$s_count]->claimed=='1' || $searchresults[$s_count]->claimed=='3'))
					unset($searchresults[$s_count]);

							$s_count++;
							continue;
						}

					

								

								if($user_bearer_token=='')  //get access token (expires in: 3600 sec)
								{

									$client = new Client();

																$row_get = DB::table('spotify_user_auth')
															->where('codetype', '=', 'access_token')
															->limit(1)
															->get();

												$user_tokens=array();

															foreach ($row_get as $row) {

																$user_tokens[$row->codetype]=$row;

															}
														
												$user_expired='0';
												if(Carbon::now()->timestamp+20-$user_tokens['access_token']->timestamp>3600) //20 seconds correction
												{
													$user_expired='1';
												}

												if($user_tokens['access_token']->codevalue!='' && $user_expired=='0')
												{

													$user_bearer_token=$user_tokens['access_token']->codevalue;

												}
												else  //is expired or not existing!
												{


									$sitenametosearch='https://artists.spotify.com/c/access/artist/'.$artisttoken;

					//1.) get page:  
					//GET:
					//https://artists.spotify.com/c/access/artist/1VZ1TofaV3kj90QJSdg6NG


					$response = $client->request('GET', $sitenametosearch, [
						'headers' => [
							
						],
					]);

					$spotifyuserpage=$response->getBody()->getContents();
					$thematches=Helperfunctions::instance()->searchInBetween('https:\/\/mrkt-web.scdn.co\/artists\/static\/js\/main.','">',$spotifyuserpage);
					$jsfile='https://mrkt-web.scdn.co/artists/static/js/main.'.$thematches; //0 is the first!


					//print_r($response->getBody()->getContents());
					//2.) get js file: 
					//GET:
					//https://mrkt-web.scdn.co/artists/static/js/main. '"'

					$js_file_response = $client->request('GET', $jsfile, [
						'headers' => [
							
						],
					]);

					//3.) get client id:
					//GET:
					//token->//AccessTokenManager.createAnonymous("6491562e26a74a4dae998b7dbaf6983f")

					$client_id_search=Helperfunctions::instance()->searchInBetween('AccessTokenManager.createAnonymous\("','"\)',$js_file_response->getBody()->getContents());
					$client_id=$client_id_search;

					//4.) get bearer token with client id:
					//POST:
					//https://generic.wg.spotify.com/creator-auth-proxy/api/token
					//  content: client_id=6491562e26a74a4dae998b7dbaf6983f&grant_type=client_credentials

					$data   = 'client_id='.$client_id.'&grant_type=client_credentials';
				

					$bearer_response = $client->request('POST', 'https://generic.wg.spotify.com/creator-auth-proxy/api/token', [
						'headers' => [
							
						],
						'body'=>$data,
					]);

					$bearer_response_json=$bearer_response->getBody()->getContents();
					$token_answer=json_decode($bearer_response_json);
					$user_bearer_token=$token_answer->access_token;




									DB::table('spotify_user_auth')
									->updateOrInsert(
								['codetype' => 'access_token'],
								['codevalue' => $user_bearer_token,'dt' => Carbon::now(),'timestamp' => Carbon::now()->timestamp]
										);
										



								}  // is expired or not existing!

					} // get access token (expires in: 3600 sec)


					//5.)
					//GET:
					//https://generic.wg.spotify.com/s4a-onboarding/v0/access/artist/1VZ1TofaV3kj90QJSdg6NG/claimed
					//With Bearer token: BQAey56c7SqK-QwdGKey51XceADc-YYv_8QquTEdkSRMOhfXdr7YiunvyU2nb9x_JxARth95H8NRF0R9lpY


						
						
					$is_claimed_response = $client->request('GET', 'https://generic.wg.spotify.com/s4a-onboarding/v0/access/artist/'.$artisttoken.'/claimed', [
						'headers' => [
							'Authorization' => "Bearer ".$user_bearer_token
						],
					]);
					$is_claimed_json=$is_claimed_response->getBody()->getContents();
					$is_claimed=json_decode($is_claimed_json);

					$claimed=0;  // 1=claimed  0=unknown 2=unclaimed 3=claimed(set by us)
					$distributorname='';
					$distributorurl='';

					$claimed=$is_claimed->isClaimed;
				
						if($claimed)
						$claimed='1';
						elseif(!$claimed)
						$claimed='2';

					$distributorname=$is_claimed->distributor->distributorName;
					$distributorurl=$is_claimed->distributor->distributorURL;


					$searchresults[$s_count]->claimed=$claimed;
					$searchresults[$s_count]->distributorname=$distributorname;
					$searchresults[$s_count]->distributorurl=$distributorurl;
							


					if($hideclaimed=='on' && ($searchresults[$s_count]->claimed=='1' || $searchresults[$s_count]->claimed=='3'))
					unset($searchresults[$s_count]);

					$s_count++;

					//sleep(1);


						//if claimed is >0 -> check if claimed else don't
						DB::table('spotify_items')
						->where('type', '=', 'artist')
						->where('itemid', '=', $artisttoken)
						->update(['claimed' => $claimed,
						'distributorname'=>$distributorname,
						'distributorurl'=>$distributorurl,
						]);
						//if claimed is >0 -> check if claimed else don't



					}

					return $searchresults;
	}

	

	public function getSingleArtistClaimState($artisttoken)
	{

	

								if($user_bearer_token=='')  //get access token (expires in: 3600 sec)
								{

									$client = new Client();

																$row_get = DB::table('spotify_user_auth')
															->where('codetype', '=', 'access_token')
															->limit(1)
															->get();

												$user_tokens=array();

															foreach ($row_get as $row) {

																$user_tokens[$row->codetype]=$row;

															}
														
												$user_expired='0';
												if(Carbon::now()->timestamp+20-$user_tokens['access_token']->timestamp>3600) //20 seconds correction
												{
													$user_expired='1';
												}

												if($user_tokens['access_token']->codevalue!='' && $user_expired=='0')
												{

													$user_bearer_token=$user_tokens['access_token']->codevalue;

												}
												else  //is expired or not existing!
												{


									$sitenametosearch='https://artists.spotify.com/c/access/artist/'.$artisttoken;

					//1.) get page:  
					//GET:
					//https://artists.spotify.com/c/access/artist/1VZ1TofaV3kj90QJSdg6NG



					$response = $client->request('GET', $sitenametosearch, [
						'headers' => [
							
						],
					]);

					$spotifyuserpage=$response->getBody()->getContents();
					$thematches=Helperfunctions::instance()->searchInBetween('https:\/\/mrkt-web.scdn.co\/artists\/static\/js\/main.','">',$spotifyuserpage);
					$jsfile='https://mrkt-web.scdn.co/artists/static/js/main.'.$thematches; //0 is the first!


					//print_r($response->getBody()->getContents());
					//2.) get js file: 
					//GET:
					//https://mrkt-web.scdn.co/artists/static/js/main. '"'

					$js_file_response = $client->request('GET', $jsfile, [
						'headers' => [
							
						],
					]);

					//3.) get client id:
					//GET:
					//token->//AccessTokenManager.createAnonymous("6491562e26a74a4dae998b7dbaf6983f")

					$client_id_search=Helperfunctions::instance()->searchInBetween('AccessTokenManager.createAnonymous\("','"\)',$js_file_response->getBody()->getContents());
					$client_id=$client_id_search;

					//4.) get bearer token with client id:
					//POST:
					//https://generic.wg.spotify.com/creator-auth-proxy/api/token
					//  content: client_id=6491562e26a74a4dae998b7dbaf6983f&grant_type=client_credentials

					$data   = 'client_id='.$client_id.'&grant_type=client_credentials';


					$bearer_response = $client->request('POST', 'https://generic.wg.spotify.com/creator-auth-proxy/api/token', [
						'headers' => [
							
						],
						'body'=>$data,
					]);

					$bearer_response_json=$bearer_response->getBody()->getContents();
					$token_answer=json_decode($bearer_response_json);
					$user_bearer_token=$token_answer->access_token;



							
									DB::table('spotify_user_auth')
									->updateOrInsert(
								['codetype' => 'access_token'],
								['codevalue' => $user_bearer_token,'dt' => Carbon::now(),'timestamp' => Carbon::now()->timestamp]
										);
										
							



								}  // is expired or not existing!

					} // get access token (expires in: 3600 sec)


					//5.)
					//GET:
					//https://generic.wg.spotify.com/s4a-onboarding/v0/access/artist/1VZ1TofaV3kj90QJSdg6NG/claimed
					//With Bearer token: BQAey56c7SqK-QwdGKey51XceADc-YYv_8QquTEdkSRMOhfXdr7YiunvyU2nb9x_JxARth95H8NRF0R9lpY


						
						
					$is_claimed_response = $client->request('GET', 'https://generic.wg.spotify.com/s4a-onboarding/v0/access/artist/'.$artisttoken.'/claimed', [
						'headers' => [
							'Authorization' => "Bearer ".$user_bearer_token
						],
					]);
					$is_claimed_json=$is_claimed_response->getBody()->getContents();
					$is_claimed=json_decode($is_claimed_json);

					$claimed=0;  // 1=claimed  0=unknown 2=unclaimed 3=claimed(set by us)
					$distributorname='';
					$distributorurl='';

					$claimed=$is_claimed->isClaimed;
				
						if($claimed)
						$claimed='1';
						elseif(!$claimed)
						$claimed='2';

					$distributorname=$is_claimed->distributor->distributorName;
					$distributorurl=$is_claimed->distributor->distributorURL;



						//if claimed is >0 -> check if claimed else don't
						DB::table('spotify_items')
						->where('type', '=', 'artist')
						->where('itemid', '=', $artisttoken)
						->update(['claimed' => $claimed,
						'distributorname'=>$distributorname,
						'distributorurl'=>$distributorurl,
						]);
						//if claimed is >0 -> check if claimed else don't


					return $claimed;
	}




	public function getGrantAccessToAccount($managerid,$manageremail,$managerpass,$cookies,$scopes_s_str,$useragent)
    {

		

				$first_cookies='';
			if($cookies!='')
				$first_cookies=$cookies;
				else
				$first_cookies=$this->getSpotifyCookies($managerid,$manageremail,$managerpass,$useragent);

		if($first_cookies=='wrong')
		return 'wrong';

				$headers=array(
					"Host: ".$this->spotifymainurl,
					"User-Agent: ".$useragent,
					"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
					"Accept-Language: en-US,en;q=0.5",
					"Accept-Encoding: gzip, deflate, br",
					"Content-Type: application/x-www-form-urlencoded",
					"Upgrade-Insecure-Requests: 1",
					"TE: Trailers"
				);
			

		$theauthurl=$this->authuri.'?client_id='.config('myconfig.spotify.clientid').'&redirect_uri='.$this->rediruri.'&response_type=code&scope='.urlencode($scopes_s_str);

			$ch=Helperfunctions::instance()->getPageCurl($theauthurl,$headers,$first_cookies,'',1,$useragent,'','',1,1);
			$cookies=Helperfunctions::instance()->getCookiesFromCurl($ch);


			$csrf_token=ltrim($cookies['csrf_token'], '=');

			$cookiesstr = '';
				$show = '';
				$head = '';
				$delim = '';
				foreach ($cookies as $k => $v){
				
					$cookiesstr .= "$delim$k$v";
				$delim = '; ';
				}

				$cookiesstr.=';'.$first_cookies;


			if($csrf_token!='')
			{
			echo 'posting';
				
				$postarray=array(
					'client_id'=>config('myconfig.spotify.clientid'),
					'redirect_uri'=>$this->rediruri,
					'response_type'=>'code',
					'scope'=>$scopes_s_str,
					'request'=> '',
					'csrf_token'=>$csrf_token
					);
					

					$builder= http_build_query($postarray);
					$postData_len = strlen($builder);

					$headers=array(
						"Host: ".$this->spotifymainurl,
						"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
						"Accept-Language: en-US,en;q=0.5",
						"Accept-Encoding: gzip, deflate, br",
						"Content-Length: ".$postData_len,
						"Content-Type: application/x-www-form-urlencoded",
						"Origin: https://".$this->spotifymainurl,
						"Connection: keep-alive",
						"Upgrade-Insecure-Requests: 1",
						"Referer: ".$theauthurl,
						"TE: Trailers"
					);
					
					$ch=Helperfunctions::instance()->simplePost($this->postauthuri,'POST',$builder,$headers,$cookiesstr,'',1,10,0,'','',$useragent);
					$post_result = curl_exec($ch);
					$last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

					$result=Helperfunctions::instance()->getPageCurl($last_url,array(),'','',0,$useragent);

			}
			else
			{
				
				$result=Helperfunctions::instance()->getPageCurl($theauthurl,$headers,$first_cookies,'',0,$useragent,'','',0,1);
			//print_r($result);
			}

			

		return $result;
	}

	
	public function getSpotifyCookies($managerid,$manageremail,$managerpass,$useragent)
    {

        $try=true;
        $cookietry=0;
        while($try)
        {

     //firstrequest
     $first_cookies = exec(config('myconfig.phantom_js_location').' /storage/spotifyartistlogin/phantomjs/getcookie.js'); //for login
     //firstrequest

     $csrf_token=Helperfunctions::instance()->searchInBetween(';csrf_token=',';__S',$first_cookies);
	
	 	
     $gRecaptchaResponse=Helperfunctions::instance()->getPageCaptcha(config('myconfig.solve_recaptcha.key'), $this->spotifygetloginurl, $this->spotifylogingooglekey);
     //print_r($gRecaptchaResponse);
     
     
     if($managerpass=='')
     {
        //problem
		echo 'Password is empty:'.$managerid;
		
		DB::table('spotify_accounts_auth')
				->where('id',$managerid)
				->update(['state' => '10']);

        exit;
	 }
	 
	 $pass=Crypt::decryptString($managerpass);

     $postarray=array(
        'remember'=>'true', 
        'continue'=> $this->spotifycontinueurl,
        'username'=>$manageremail,
        'password'=>$pass,
        'recaptchaToken'=>$gRecaptchaResponse,
        'csrf_token'=>$csrf_token
        );
        $builder= http_build_query($postarray);
        $postData_len = strlen($builder);

        $headers=array(
            "Host: ".$this->spotifymainurl,
            "Accept: application/json, text/plain, */*",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate, br",
            "Content-Length: ".$postData_len,
            "Content-Type: application/x-www-form-urlencoded",
            "Origin: ".$this->spotifymainurl,
            "Connection: keep-alive",
            "Referer: https://".$this->spotifygetloginurl,
            "TE: Trailers"
        );

     $ch=Helperfunctions::instance()->simplePost($this->spotifypostloginurl,'POST',$builder,$headers,$first_cookies,'',1,0,1,'','',$useragent);
     $cookies=Helperfunctions::instance()->getCookiesFromCurl($ch);
     
    if($cookies['sp_dc']=='')
    {

        //problem
        

        echo 'Problem with getting cookies for manager, trying again:'.$managerid."\n";

        if($cookietry=='5')
        {

		 echo 'After 5 tries, unable to get cookies:'.$managerid."\n";
		 
		 DB::table('spotify_accounts_auth')
				->where('id',$managerid)
				->update(['state' => '10']);

				return 'wrong';
        }
    }
    else
    {
        $try=false; //exit the loop cause dc cookie found!
    }

    $cookietry++;        
        }

     $cookiesstr = '';
     $show = '';
     $head = '';
     $delim = '';
     foreach ($cookies as $k => $v){
    
        $cookiesstr .= "$delim$k$v";
       $delim = '; ';
     }

        //save to db
        DB::table('spotify_accounts_auth')
				->where('id',$managerid)
				->update(['cookies' => $cookiesstr,
						  'useragent'=>$useragent]);

        //save to db


        return $cookiesstr;

	}
	

	public function addItemToDB($spotifyapi,$itemid,$itemtype)
	{


		$theitem=$this->getSpotifyItem($spotifyapi,$itemid,$itemtype);

		if(empty($theitem))
		return;

		if($itemtype=='playlist')
		{
			$imageurl='';
			if($theitem->images[2]->url)
			$imageurl=$theitem->images[2]->url;
			elseif($theitem->images[0]->url)
			$imageurl=$theitem->images[0]->url;

			DB::table('spotify_items')
					->updateOrInsert(
				['type' => $itemtype,
				 'itemid' => $theitem->id],
				[
					'name' => mb_substr($theitem->name,0, 500,'UTF-8'),
					'followercount' => $theitem->followers->total,
					'imageurl' => $imageurl,
					'url' => $theitem->external_urls->spotify,
					'ownerurl' => $theitem->owner->external_urls->spotify,
					'ownername' => mb_substr($theitem->owner->display_name,0, 500,'UTF-8'),
					'description' => $theitem->description,
					'collaborative' => $theitem->collaborative,
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
								
		

		}
		elseif($itemtype=='album')
		{
			$imageurl='';
			if($theitem->images[2]->url)
			$imageurl=$theitem->images[2]->url;
			elseif($theitem->images[0]->url)
			$imageurl=$theitem->images[0]->url;

			DB::table('spotify_items')
					->updateOrInsert(
				['type' => $itemtype,
				 'itemid' => $theitem->id],
				[
					'name' => mb_substr($theitem->name,0, 500,'UTF-8'),
					'genres' => implode(', ', $theitem->genres),
					'popularity' => $theitem->popularity,
					//'artisturl' => $theitem->artists[0]->external_urls->spotify,
					//'artistname' => $theitem->artists[0]->name,
					//'albumtype'=> $theitem->album_type,
					//'release_date'=> $theitem->release_date,
					'imageurl' => $imageurl,
					'url' => $theitem->external_urls->spotify,
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



			DB::table('spotify_accounts_auth_realalbums')
								->updateOrInsert(
							['spid' => $theitem->id],
							[
								'name' => mb_substr($theitem->name,0, 500,'UTF-8'),
								'artistid' => $theitem->artists[0]->id,
								'artistname' => $theitem->artists[0]->name,
								'albumtype'=> $theitem->album_type,
								'release_date'=> $theitem->release_date,
								'label'=> $theitem->label,
							'dt' => Carbon::now()]
									);
									
				
									$last_id='';
									$last_id = DB::getPdo()->lastInsertId();
						
									if($last_id>0)
											{
												DB::table('spotify_accounts_auth_realalbums')
												->where('id', '=', $last_id)
												->update(['timestamp' => Carbon::now()->timestamp]);
						
						
											}

				if($theitem->artists[0]->id!='')
				$this->addItemToDB($spotifyapi,$theitem->artists[0]->id,'artist');
		


		}
		elseif($itemtype=='track')
		{
			
			$imageurl='';
			if($theitem->album->images[2]->url)
			$imageurl=$theitem->album->images[2]->url;
			elseif($theitem->album->images[0]->url)
			$imageurl=$theitem->album->images[0]->url;

			DB::table('spotify_items')
					->updateOrInsert(
				['type' => $itemtype,
				 'itemid' => $theitem->id],
				[
					'name' => mb_substr($theitem->name,0, 500,'UTF-8'),
					'popularity' => $theitem->popularity,
					//'artisturl' => $theitem->album->artists[0]->external_urls->spotify,
					//'artistname' => $theitem->album->artists[0]->name,
					//'albumurl' => $theitem->album->external_urls->spotify,
					//'albumname' => $theitem->album->name,
					'imageurl' => $imageurl,
					'url' => $theitem->external_urls->spotify,
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

			
		$secondtrackdata=$this->getAudioFeatures($spotifyapi,$itemid);


				DB::table('spotify_accounts_auth_realtracks')
				->updateOrInsert(
			['spid' => $theitem->id],
			[
				'name' => mb_substr($theitem->name,0, 500,'UTF-8'),
				'artistid' => $theitem->album->artists[0]->id,
				'artistname' => $theitem->album->artists[0]->name,
				'albumid' => $theitem->album->id,
				'albumname' => $theitem->album->name,
				'info_tempo'=>$secondtrackdata->audio_features[0]->tempo,
				'info_key'=>$secondtrackdata->audio_features[0]->key,
				'info_danceability'=>$secondtrackdata->audio_features[0]->danceability,
				'info_duration_ms'=>$secondtrackdata->audio_features[0]->duration_ms,
			'dt' => Carbon::now()]
					);
					

					$last_id='';
					$last_id = DB::getPdo()->lastInsertId();
		
					if($last_id>0)
							{
								DB::table('spotify_accounts_auth_realtracks')
								->where('id', '=', $last_id)
								->update(['timestamp' => Carbon::now()->timestamp]);
		
		
							}



					if($theitem->album->id!='')
					$this->addItemToDB($spotifyapi,$theitem->album->id,'album');
	

		}
		elseif($itemtype=='show')
		{
			$imageurl='';
			if($theitem->images[2]->url)
			$imageurl=$theitem->images[2]->url;
			elseif($theitem->images[0]->url)
			$imageurl=$theitem->images[0]->url;

			DB::table('spotify_items')
					->updateOrInsert(
				['type' => $itemtype,
				 'itemid' => $theitem->id],
				[
					'name' => mb_substr($theitem->name,0, 500,'UTF-8'),
					'url' => $theitem->external_urls->spotify,
					'imageurl' => $imageurl,
					'description' => $theitem->description,
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


		}
		elseif($itemtype=='episode')
		{
			
			$imageurl='';
			if($theitem->images[2]->url)
			$imageurl=$theitem->images[2]->url;
			elseif($theitem->images[0]->url)
			$imageurl=$theitem->images[0]->url;

			DB::table('spotify_items')
					->updateOrInsert(
				['type' => $itemtype,
				 'itemid' => $theitem->id],
				[
					'name' => mb_substr($theitem->name,0, 500,'UTF-8'),
					'url' => $theitem->external_urls->spotify,
					'imageurl' => $imageurl,
					'description' => $theitem->description,
					//'release_date'=> $theitem->release_date,
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

								if($theitem->show->id!='')
								$this->addItemToDB($spotifyapi,$theitem->show->id,'show');


		}
		elseif($itemtype=='artist')
		{
			
			$imageurl='';
			if($theitem->images[2]->url)
			$imageurl=$theitem->images[2]->url;
			elseif($theitem->images[0]->url)
			$imageurl=$theitem->images[0]->url;

			DB::table('spotify_items')
					->updateOrInsert(
				['type' => $itemtype,
				 'itemid' => $theitem->id],
				[
					'name' => mb_substr($theitem->name,0, 500,'UTF-8'),
					'followercount' => $theitem->followers->total,
					'genres' => implode(', ', $theitem->genres),
					'popularity' => $theitem->popularity,
					'imageurl' => $imageurl,
					'url' => $theitem->external_urls->spotify,
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

		}





	}


	public function getTracksFromPlaylists($managerid,$spid)
	{


		DB::table('spotify_accounts_auth_realplaylists')
				->where('spid', $spid)
				->update(['trackgetstate' => '2']);


				echo '...trying to get tracks for:'.$spid.'...'."\n";

				$myspotifyapi=$this->getMySpotifyTokens($managerid);


				$tracks=array();
                $tracks=$this->getPlaylistTracks($myspotifyapi,$spid);

                if(empty($tracks))
                {
                echo 'tracks object is empty';
                return false;
                }

				$track_count=0;
              $dupl_check_tracks=array();
			  $snapshotid='';
			  

			  foreach($tracks as $tracks_s)
                    {

                        if(in_array($tracks_s->track->id,$dupl_check_tracks))
                        {
                        echo 'duplicate found:'.$tracks_s->track->id.', at position:'.$track_count.', removing...'."\n";
                     
                        $delete_tracksarray=array();
                         $currenttrack=array();
                         $currenttrack['id']=$tracks_s->track->id;
                         $currenttrack['positions'][]=$track_count;
                         $delete_tracksarray['tracks'][]=$currenttrack;

                         $snapshotid=$this->deletePlaylistTracks($myspotifyapi,$spid, $delete_tracksarray,$snapshotid);
                         continue;
                        }

                        $this->addItemToDB($myspotifyapi,$tracks_s->track->id,'track');

                        $getTrackRecord = DB::table('spotify_accounts_auth_realtracks')
                                    ->where('spid', '=', $tracks_s->track->id)
                                    ->first();
                
                                    $realtrackid='';
                                    if($getTrackRecord->id >0)
                                    {
                
                                        $realtrackid=$getTrackRecord->id;
                
                                    }
                                    

                        DB::table('spotify_trackplaylist_fk')
                                    ->updateOrInsert(
                                ['playlist_id' => $spid,
                                'track_id' => $realtrackid],
                                [ 
                                'position' => $track_count
                                    ]
                                         );
                                        

                                        $dupl_check_tracks[]=$tracks_s->track->id;
                    
                                        $track_count++;
                    }



		DB::table('spotify_accounts_auth_realplaylists')
				->where('spid', $spid)
				->update(['trackgetstate' => '1']);

				echo 'Getting tracks is successful.';
				return true;

	}

	public function addGenrePlaylistsToProfile($artistid=0)
	{


		$get_accounts=DB::table('spotify_accounts_auth as t1')
            ->select('t1.*','t3.spid AS artistid','t3.genreplaylistpoststate','t4.id AS theitemid')
            ->leftJoin('spotify_accounts_auth_realartists AS t3', function($join)
             {
                     $join->on('t1.artistconnectid', '=', 't3.id');
             })
             ->leftJoin('spotify_items AS t4', function($join)
             {
                     $join->on('t3.spid', '=', 't4.itemid');
             })
            ->where('t1.userid', '=', '1')
			->where('t3.spid','!=', '')
			->where('t3.spid','!=', '')
            ->where(function ($query) use($artistid) {
				if($artistid==0)
				{
                $query->where('t3.genreplaylistpoststate', '=', '0')
					->orWhereNull('t3.genreplaylistpoststate');
				}
			})
			->where(function ($query) use($artistid) {
				if($artistid!=0)
				{
                $query->where('t3.spid', '=', $artistid);
				}
            })
            ->whereNotNull('t3.spid')
            ->orderByRaw('t1.id ASC')
            ->limit(1)
            ->get();

            foreach($get_accounts as $get_accounts_s)
            {
                $allaccounts[]=$get_accounts_s;


            }

$managerid=$allaccounts[0]->id;
$artistid=$allaccounts[0]->artistid;
$myUserAgent=$allaccounts[0]->useragent;
$theitemid=$allaccounts[0]->theitemid;

            if(empty($allaccounts))
            {
                echo 'no more accounts left'."\n";
                return;
            }


            echo '...getting manager:'.$managerid.' and artist:'.$artistid."\n";

            $row_get_genres = DB::table('spotify_itemgenre_fk AS t1')
            ->select('t1.*')
                    ->where('t1.item_id','=',$theitemid)
                    ->get();


                    foreach( $row_get_genres as  $row_get_genres_s)
                    {
                        $genres[]=$row_get_genres_s;
                    }
                    

                    if(empty($genres))
                    {

                        echo 'genres are empty'."\n";
                        DB::table('spotify_accounts_auth_realartists')
                                  ->where('spid',$artistid)
                                  ->update(['genreplaylistpoststate' => '1']);
                                  return;


                    }


                    foreach( $genres as  $genres_s)
                    {
        

            $row_get = DB::table('spotify_accounts_auth_realplaylists AS t1')
                    ->select('t1.*','t1.spid AS playlistitemid','t2.name AS genrename')
                    ->leftJoin('spotify_genres AS t2', function($join)
							{
                            $join->on('t1.id', '=', 't2.playlistid')
                            ->where('t1.type', '=', 'genreplaylist');
                            })
                            ->where('t2.id','=',$genres_s->genre_id)
                            ->limit(1)
                            ->get();

                            
                            
                            foreach($row_get as $row_get_s)
                            {

                                echo 'putting genre to artist profile: '.$row_get_s->genrename."\n";

            $this->putThePlaylistToArtistPage($managerid,$row_get_s->playlistitemid,$artistid,$myUserAgent,'genreplaylist');
                            }

            

                    }



                    echo 'adding to profile successful'."\n";
                        DB::table('spotify_accounts_auth_realartists')
                                  ->where('spid',$artistid)
                                  ->update(['genreplaylistpoststate' => '1']);
								  return true;




	}


	public function putThePlaylistToArtistPage($managerid,$playlistspid,$artistid,$myUserAgent,$playlisttype)
    {

        
        $bearertoken=$this->getArtistProfileBearerToken($managerid);
        //get current playlists
        
        $headers=array(
            "authorization: Bearer ".$bearertoken,
            "Content-Type: application/json"
        );

        $url='https://generic.wg.spotify.com/artist-identity-view/v2/profile/'.$artistid.'?fields=playlists&application=s4a';

        $result=Helperfunctions::instance()->getPageCurl($url,$headers,'','',0,$myUserAgent);

        $json_result = json_decode($result, true);


        $playlistsarray=array();
       $playlistsarray=$json_result['playlists']['data'];



           $includeourown=1;
       $uris=array();
      
       if(!empty($playlistsarray))
       {
            foreach($playlistsarray as $playlistsarray_s)
            {
                
            if(strpos($playlistsarray_s['uri'], 'playlist:'.$playlistspid) !== false)
                $includeourown=0;

                $uris[]=$playlistsarray_s['uri'];
            }
        }

       if($includeourown=='0')
        {
           echo "Url already exists so not adding again";
            //exit;

        }
        else
        {

            $uris[]='spotify:playlist:'.$playlistspid;
        }

       


        $data=json_encode(array(
            'uris'=>$uris,
            "sortOrder"=>"presorted"
        ));
        
        $headers=array(
            "authorization: Bearer ".$bearertoken,
            "Content-Type: application/json"
        );

        $url='https://generic.wg.spotify.com/artist-identity-view/v1/profile/'.$artistid.'/playlists?organizationUri=spotify:artist:'.$artistid;

        
        $result=Helperfunctions::instance()->simplePost($url,'PUT',$data,$headers,'','',0,0,0,'','',$myUserAgent);
  
        if(!$result)
        {
            echo "There were some problem with adding playlist to profile page";
			if($playlisttype=='genreplaylist')
			{
                        DB::table('spotify_accounts_auth_realartists')
                                  ->where('spid',$artistid)
								  ->update(['genreplaylistpoststate' => '10']);
			}
							return;
        }
        else
        {
            echo "Playlist successfully posted to profile page";

        }

     



    }

     public static function instance()
     {
         return new SpotifyHelper();
     }
}