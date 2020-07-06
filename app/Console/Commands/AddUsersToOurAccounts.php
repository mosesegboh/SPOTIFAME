<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use App\Helpers\UserAgentHelper;

use Illuminate\Support\Facades\Crypt;

use Carbon\Carbon;

class AddUsersToOurAccounts extends Command
{
    
    private $proxies;
    private $reg_surnames=array();
    private $reg_boys=array();
    private $reg_girls=array();
    private $reg_domains=array();

    private $spotifymainurl;
    private $spotifygooglekey;
    private $spotifygetregurl;
    private $spotifypostregurl;
    private $spotifyvalidateemailurl;
    private $creationpointurl;
    private $spotifyposthost;

    private $scopes;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:adduserstoouraccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Users To Our Accounts and User Playlists And Post them To Profile';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->proxies=array(
            array('proxy'=>'163.172.48.109','proxyport'=>'15002'),
            array('proxy'=>'163.172.48.117','proxyport'=>'15002'),
            array('proxy'=>'163.172.48.119','proxyport'=>'15002'),
            array('proxy'=>'163.172.48.121','proxyport'=>'15002'),
            array('proxy'=>'163.172.36.181','proxyport'=>'15002'),
            array('proxy'=>'163.172.36.191','proxyport'=>'15002'),
            array('proxy'=>'62.210.251.228','proxyport'=>'15002'),
            array('proxy'=>'163.172.36.207','proxyport'=>'15002')
        );

        $this->spotifygooglekey='6Lenb9oUAAAAAO1Rqrm4KsoNH14OvMm6SWkQcdRj';
        $this->spotifygetregurl='https://www.spotify.com/us/signup/';
        $this->spotifypostregurl='https://spclient.wg.spotify.com/signup/public/v1/account';
        $this->creationpointurl='https://www.spotify.com/us/';

        $this->spotifymainurl='https://www.spotify.com';
        $this->spotifyvalidateemailurl='https://spclient.wg.spotify.com/signup/public/v1/account?validate=1&email=';
        $this->spotifyposthost='spclient.wg.spotify.com';

        $this->scopes= ['user-read-email',
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
                ];

                

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
       
        ini_set('max_execution_time', 1200); // 600 = 10 minutes

        ini_set('memory_limit', '1024M');

        /*
        foreach ($this->proxies as $proxies_s)
        {
            echo $proxies_s['proxy'];
        }
        */
        $randproxy=rand(0,count($this->proxies)-1);

        $myUserAgent = UserAgentHelper::instance()->generate();

        $proxyip=$this->proxies[$randproxy]['proxy'];
        $proxyport=$this->proxies[$randproxy]['proxyport'];
        //echo $proxyaddr.':'.$proxyport;

        $scopes_s_str='';
                $delim='';
                foreach ($this->scopes as $scopes_s)
                {

                    $scopes_s_str.=$delim.$scopes_s;
                    $delim=' ';
                }
        

        $inputFileName = "storage/spotifyusergenerator/usercreator.xlsx";

        /**  Identify the type of $inputFileName  **/
        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);

        /**  Create a new Reader of the type that has been identified  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

        /**  Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($inputFileName);

        /**  Convert Spreadsheet Object to an Array for ease of use  **/
        $lines = $spreadsheet->getActiveSheet()->toArray();

        $linenumber=0;
        foreach( $lines as $single_line )
        {
            if($linenumber==0)
            {
                $linenumber++;
                continue;
            } 
            /*
            echo '<div class="row">';
            foreach( $single_line as $single_item )
            {
                echo '<p class="item">' . $single_item . '</p>';
            }
            exit;
            echo '</div>';
            */
           
            
                if(trim($single_line[0])!='')
                $this->reg_surnames[]=trim($single_line[0]);

                if(trim($single_line[1])!='')
                $this->reg_boys[]=trim($single_line[1]);

                if(trim($single_line[2])!='')
                $this->reg_girls[]=trim($single_line[2]);

                if(trim($single_line[3])!='')
                $this->reg_domains[]=trim($single_line[3]);
                

            $linenumber++;
        }
            /*
            print_r($this->reg_surnames);
            print_r($this->reg_boys);
            print_r($this->reg_girls);
            print_r($this->reg_domains);
            */

//$this->putTopTracks('575','0iwydZl3K5P8SY3eK1q3ZY','1C1x4MVkql8AiABuTw6DgE');exit;


//$this->putTopTracks('591','4jXr8y7Xjv1MNwQEypZsdq','0UmBaQJflaHddKtf1lrA6F');exit;

//$this->putThePlaylistToArtistPage('29','4jXr8y7Xjv1MNwQEypZsdq','0UmBaQJflaHddKtf1lrA6F','Mozilla/5.0 (Android; Android 5.1; MOTO E XT1021 Build/LMY47Z) AppleWebKit/537.49 (KHTML, like Gecko)  Chrome/51.0.1608.314 Mobile Safari/601.2');exit;

            $useridtoinsertto='1';

            $get_accounts=DB::table('spotify_accounts_auth as t1')
            ->select('t1.*','t3.spid AS artistid','t4.name AS artistname')
            ->leftJoin('spotify_accounts_auth_realartists AS t3', function($join)
             {
                     $join->on('t1.artistconnectid', '=', 't3.id');
             })
             ->leftJoin('spotify_items AS t4', function($join)
             {
                     $join->on('t3.spid', '=', 't4.itemid');
             })
            ->where('t1.userid', '=', $useridtoinsertto)
            ->where('t3.spid','!=', '')
            ->whereNotNull('t3.spid')
            ->orderByRaw('t1.id ASC')
            ->get();

            //select `t1`.*, `t3`.`spid` as `artistid`, `t4`.`name` as `artistname` from `spotify_accounts_auth` as `t1` left join `spotify_accounts_auth_realartists` as `t3` on `t1`.`artistconnectid` = `t3`.`id` left join `spotify_items` as `t4` on `t3`.`spid` = `t4`.`itemid` where `t1`.`userid` = 1 and `t3`.`spid` !='' and `t3`.`spid` is not null order by t1.id ASC 
    
            foreach($get_accounts as $get_accounts_s)
            {

                //get account which hasn't got Artist Playlist
                    $check_accounts=DB::table('spotify_accounts_auth as t1')
                    ->select('t1.*')
                    ->where('t1.userid', '=', $useridtoinsertto)
                    ->where('t1.artistplaylistaccount', '=', '1')
                    ->where('t1.main_sp_account', '=', $get_accounts_s->id)
                    ->orderByRaw('t1.id ASC')
                    ->limit(1)
                    ->get();

                    $foundornot='';
                    foreach($check_accounts as $check_accounts_s)
                    {
                       $foundornot=$check_accounts_s->id;
                    }

                    if($foundornot=='')
                    $empty_useful_ids[]=$get_accounts_s;


            }

            if(empty($empty_useful_ids))
            {
                echo 'no more account found';exit;
            }

            $artistid=$empty_useful_ids[0]->artistid;
            $main_sp_account_spid=$empty_useful_ids[0]->spid;
            $main_sp_account_id=$empty_useful_ids[0]->id;
            $main_sp_account_name=$empty_useful_ids[0]->artistname;

            $insertable_playlistname=$main_sp_account_name.' Playlist';

           
            

        //get account which hasn't got Artist Playlist

            //get last inserted generated
            $lastinsertedresultset=DB::table('spotify_accounts_auth as t1')
            ->select('t1.*')
            ->where('t1.userid', '=', $useridtoinsertto)
            ->where('t1.artistplaylistaccount', '=', '1')
            ->orderByRaw('t1.id DESC')
            ->limit(1)
            ->get();
            //get last inserted generated
            foreach($lastinsertedresultset as $lastinsertedresultset_s)
            {
                $theresult=$lastinsertedresultset_s;
            }


            $lastinserteddomain=explode('@',$theresult->email)[1];

            $reg_d_index = array_search($lastinserteddomain, $this->reg_domains);

            if(!$theresult->email)
            $reg_d_index=0;
            

            if($reg_d_index==0)
            $prevdomain=$this->reg_domains[count($this->reg_domains)-1];
            else
            {
if($reg_d_index !== false && $reg_d_index > 0 ) $prevdomain = $this->reg_domains[$reg_d_index-1];
            }
            
            if($reg_d_index==count($this->reg_domains)-1)
            $nextdomain = $this->reg_domains[0];
            else
            {
if($reg_d_index !== false && $reg_d_index < count($this->reg_domains)-1) $nextdomain = $this->reg_domains[$reg_d_index+1];
            }

// echo $prevdomain.' '.$nextdomain;exit;

            //domain
            $insertabledomain=$nextdomain;

            //pass
            $generatedpass=Helperfunctions::instance()->generatePasswd(6,2);
            

            //get unique email
            $uniqueaccount=$this->getUniqueAccount($insertabledomain);
            
            $displayname='Artist Playlist';
            $chosengender=$uniqueaccount['gender'];
            $chosenemail=$uniqueaccount['email'];

            /*
            echo $displayname."\n";
            echo $chosenemail."\n";
            echo $chosengender."\n";
            echo $proxyip."\n";
            echo $proxyport."\n";
            echo $myUserAgent."\n";
            echo $generatedpass."\n";
            */
            


            //firstrequest
            $first_cookies = exec(config('myconfig.phantom_js_location').' /storage/spotifyregistration/phantomjs/getcookiereg.js "'.$myUserAgent.'" "'.$proxyip.':'.$proxyport.'"'); //for login
            //$first_cookies = exec(config('myconfig.phantom_js_location').' /storage/spotifyregistration/phantomjs/getcookiereg.js'); //for login
            //firstrequest
           // print_r($first_cookies);exit;
         
        $page=Helperfunctions::instance()->getPageCurl($this->spotifygetregurl,array(),$first_cookies,'',0,$myUserAgent,$proxyip,$proxyport);
        $signupkey=Helperfunctions::instance()->searchInBetween('"signupServiceAppKey":"','"',$page);


        if($signupkey=='')
            {
                echo '...Problem with signup key...';

                exit;
            }

            $postarray=array(
                'birth_day'=>rand(1,12), 
                'birth_month'=> str_pad(rand(1,12),2,0,STR_PAD_LEFT),
                'birth_year'=>rand(1970,2002),
                'creation_flow'=>'',
                'creation_point'=>$this->creationpointurl,
                'displayname'=>$displayname,
                'email'=>$chosenemail,
                'gender'=>$chosengender,
                'iagree'=>'1',
                'key'=>$signupkey,
                'password'=>$generatedpass,
                'password_repeat'=>$generatedpass,
                'platform'=>'www',
                'referrer'=>'',
                'send-email'=>1,
                'thirdpartyemail'=>1,
                'fb'=>0,
                );
                $builder= http_build_query($postarray);
                $postData_len = strlen($builder);
               
                $headers=array(
                    "Host: ".$this->spotifyposthost,
                    "Accept: */*",
                    "Accept-Language: en-US,en;q=0.5",
                    "Accept-Encoding: gzip, deflate, br",
                    "Content-Length: ".$postData_len,
                    "Content-Type: application/x-www-form-urlencoded",
                    "Origin: ".$this->spotifymainurl,
                    "Connection: keep-alive",
                    "Referer: ".$this->spotifygetregurl,
                    "TE: Trailers"
                );


                $result=Helperfunctions::instance()->simplePost($this->spotifypostregurl,'POST',$builder,$headers,'','',0,0,0,$proxyip,$proxyport,$myUserAgent);
    
            $json_result = json_decode($result, true);

            if($json_result['status']=='1')
                {
                    $generatedpass=Crypt::encryptString($generatedpass);
                    $spid=$json_result['username'];
                    $spurl='https://open.spotify.com/user/'.$json_result['username'];
                    
                    if($spid!='')
                        {
                    DB::table('spotify_accounts_auth')
								->insert([
								'displayname' => $displayname,
                                'email' => $chosenemail,
                                'spid'=>$spid,
                                'url'=>$spurl,
                                'userid'=>$useridtoinsertto,
                                'thingstr'=>$generatedpass,
                                'artistplaylistaccount'=>'1',
                                'main_sp_account'=>$main_sp_account_id,
                                'useragent'=>$myUserAgent,
                                'dt' => Carbon::now(),
                                'timestamp' => Carbon::now()->timestamp
                            ]);
                            echo '...New account successfully inserted...';


                            $last_id='';
                        $last_id = DB::getPdo()->lastInsertId();
                        
                            echo '...Now getting tokens from spotify by logging in...';
                            $result=SpotifyHelper::instance()->getGrantAccessToAccount($last_id,$chosenemail,$generatedpass,'',$scopes_s_str,$myUserAgent);
   

                                if($result=='good')
                                {
                                    DB::table('spotify_accounts_auth')
                                                ->where('id',$last_id)
                                                ->update(['state' => '1',
                                                        'scopes' => $scopes_s_str]);

                                     echo '...Creating playlist...';
                                     $theplaylist=$this->creatingPlaylist($useridtoinsertto,$last_id,$insertable_playlistname);

                                        if(!empty($theplaylist))
                                        {
                                        echo '...Putting tracks to playlist...';
                                        $puttop_response=$this->putTopTracks($last_id,$theplaylist->id,$artistid);

                                            if($puttop_response)
                                            {

                                               
                                                SpotifyHelper::instance()->putThePlaylistToArtistPage($main_sp_account_id,$theplaylist->id,$artistid,$myUserAgent,'artistplaylist');
   

                                                //add genreplaylists to Artist Profile
                                                $addgenreplaylisttoprofile=SpotifyHelper::instance()->addGenrePlaylistsToProfile($artistid);

                                            }

                                        }
                                        else
                                        {
                                        echo '...There were some problems with the playlist...';

                                        }

                                    
                                }
                                else
                                {

                                    //problem
                                    DB::table('spotify_accounts_auth')
                                                ->where('id',$last_id)
                                                ->update(['state' => '0']);
                                }

                        }
                        else
                         {
                            echo '...Problem: Sp ID not good...';

                         }
                           

                }
                else
                {
                    print_r($json_result);

                    echo '...Some problems occured...';


                }


    }


    public function checkEmailAtSpotify($emailaddress)
    {
        
        $url=$this->spotifyvalidateemailurl.$emailaddress;

            $result=Helperfunctions::instance()->getPageCurl($url);

        $json_result = json_decode($result, true);

            return $json_result['status'];

    }

    public function getUniqueAccount($insertabledomain)
    {
            

            $try=true;
            while($try)
            {
                $accounttocheck=array();
                $accounttocheck=$this->getRandomVariation($insertabledomain,rand(1,6));
                $checkifexists=DB::table('spotify_accounts_auth as t1')
                ->select('t1.*')
                ->where('t1.email', '=', $accounttocheck['email'])
                ->limit(1)
                ->get();
                
                $checkifexists_res=array();
                foreach($checkifexists as $checkifexists_s)
                {
                    $checkifexists_res[]=$checkifexists_s;
                }

                if(empty($checkifexists_res) && $this->checkEmailAtSpotify($accounttocheck['email'])=='1')
                {
                   
                    //if unique
                    $try=false;
                }

                
            }
            
            return $accounttocheck;


    }

    public function getRandomVariation($insertabledomain,$variation)
    {
            /*
            girlsname@domain
            boysname@domain
            boysname.surname@domain
            girlsname.surname@domain
            girlsname+ (a random letter a-z) @domain (example annab@domain)
            boysname+ (a random letter a-z) @domain (example johnb@domain)
            */

            $surname=strtolower($this->reg_surnames[rand(0,count($this->reg_surnames)-1)]);
    
            $account=array();
            if($variation=='1')
            {
                $firstname=strtolower($this->reg_girls[rand(0,count($this->reg_girls)-1)]);
                $account['email']=$firstname.'@'.$insertabledomain;
                $account['gender']='female';
            }
            elseif($variation=='2')
            {
                $firstname=strtolower($this->reg_boys[rand(0,count($this->reg_boys)-1)]);
                $account['email']=$firstname.'@'.$insertabledomain;
                $account['gender']='male';
            }
            elseif($variation=='3')
            {
                $firstname=strtolower($this->reg_boys[rand(0,count($this->reg_boys)-1)]);
                $account['email']=$firstname.'.'.$surname.'@'.$insertabledomain;
                $account['gender']='male';
            }
            elseif($variation=='4')
            {
                $firstname=strtolower($this->reg_girls[rand(0,count($this->reg_girls)-1)]);
                $account['email']=$firstname.'.'.$surname.'@'.$insertabledomain;
                $account['gender']='female';
            }
            elseif($variation=='5')
            {
                $firstname=strtolower($this->reg_girls[rand(0,count($this->reg_girls)-1)]);
                $azletter = chr(rand(97,122));
                $account['email']=$firstname.$azletter.'@'.$insertabledomain;
                $account['gender']='female';
            }
            elseif($variation=='6')
            {
                $firstname=strtolower($this->reg_boys[rand(0,count($this->reg_boys)-1)]);
                $azletter = chr(rand(97,122));
                $account['email']=$firstname.$azletter.'@'.$insertabledomain;
                $account['gender']='male';
            }
    
            $account['displayname']=ucfirst($firstname).' '.ucfirst($surname);

           
            return $account;
    
    
    }


    public function creatingPlaylist($useridtoinsertto,$managerid,$insertable_playlistname)
    {


        $myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($managerid);

        $try=true;
            while($try)
            {
              try {
  
                          $myrealplaylists_s=$myspotifyapi->createPlaylist([
                              'name' => $insertable_playlistname
                          ]);
                          $try=false;
                  
              } catch (\Exception $e) {
  
                          if ($e->getCode() == 429) {
                              echo 'Rate limit, trying again:'.$managerid."\n";
  
                          }
                          else
                          {
                          echo 'spotify account is disconnected either by user or spotify'.$e->getCode();
                          DB::table('spotify_accounts_auth')
                      ->where('id', $managerid)
                      ->update(['state' => '10']);
                          $try=false;
                          }
  
  
                  }
  
              }




              DB::table('spotify_accounts_auth_realplaylists')
					->insert(
				[
                'spid' => $myrealplaylists_s->id,
				'dt' => Carbon::now(),
				'userid'=>$useridtoinsertto,
                'managerid'=>$managerid,
                'type'=>'artistplaylist',
                'artistplayliststate'=>'0']
						);

						$playlistid='';
						$playlistid = DB::getPdo()->lastInsertId();
			
						if($playlistid>0)
								{
									DB::table('spotify_accounts_auth_realplaylists')
									->where('id', '=', $playlistid)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
                                }
                                
                   
             DB::table('spotify_items')
					->insert(
				['type' => 'playlist',
				 'itemid' => $myrealplaylists_s->id,
					'name' => mb_substr($myrealplaylists_s->name,0, 500,'UTF-8'),
					'followercount' => $myrealplaylists_s->followers->total,
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
                                

                                
          return $myrealplaylists_s;


    }

    public function putTopTracks($managerid,$playlistspid,$artistid)
    {

        $myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($managerid);
        //get albums
        $albums=SpotifyHelper::instance()->getAllArtistAlbums($myspotifyapi,$artistid);
        if(empty($albums))
        {
        echo 'album object is empty';
        exit;
        }

//print_r(count($albums));exit;
/*
foreach($albums as $albums_s)
{
echo $albums_s->name.' ('.$albums_s->id.')'."\n";
}
*/

        //get albums
        foreach($albums as $albums_s)
        {
            $getalbums[]=$albums_s->id;
        }
        $getalbums=array_unique($getalbums);



        //get tracks
        $tracks=array();
        foreach($getalbums as $getalbums_s)
        {
            //echo $getalbums_s;

            $myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($managerid);
            $getetracksnow=SpotifyHelper::instance()->getAllAlbumTracks($myspotifyapi,$managerid,$getalbums_s);
           
            if(!empty($getetracksnow))
            {
                foreach ($getetracksnow as $getetracksnow_s)
                {
                    array_push($tracks,$getetracksnow_s);
                }
            
            }
           
        }

        if(empty($tracks))
        {
        echo 'track object is empty';
        exit;
        }
        

        //sort by popularity
        foreach($tracks as $tracks_s)
        {
            $gettracks[]=$tracks_s->id;
        }
        $gettracks=array_unique($gettracks);

        //echo count($gettracks);

        foreach($gettracks as $gettracks_s)
        {
            //echo $getalbums_s;
            $myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($managerid);
            $getetrackinformation=SpotifyHelper::instance()->getSpotifyItem($myspotifyapi,$gettracks_s,'track');
    
           
            if(!empty($getetrackinformation))
            {
                
                $needsorttracks[]=$getetrackinformation;
                
            }
           
        }
        //echo $needsorttracks[0]->popularity;

        

        usort($needsorttracks, function($a, $b) {return $a->popularity < $b->popularity;});

        $finaltracks=array();
        $previous_names=array();

        //sort out duplicates
foreach ($needsorttracks as $needsorttracks_s)
{

    $includesong=1;
    foreach ($previous_names as $previous_name)
    {
        if(stripos($previous_name, $needsorttracks_s->name) !== false || stripos($needsorttracks_s->name, $previous_name) !== false)
        $includesong=0;

    }
    if($includesong)
    $finaltracks[]=$needsorttracks_s;

    $previous_names[]=$needsorttracks_s->name;


}
        //sort out duplicates

//echo $finaltracks[2]->popularity;exit;

         $alltrackcount=count($finaltracks);

        if($alltrackcount<=40)
        {

            $topelements=$finaltracks;
        }
        else
        {
            
            $randamount=rand(40,60);

            if($randamount>$alltrackcount)
            $topelements=$finaltracks;
            else
            $topelements=array_slice($finaltracks, 0, $randamount);


        }

        $fill_for_dupl_check_ids=array();
        foreach ($topelements as $topelements_s)
        {

            if(!in_array($topelements_s->id,$fill_for_dupl_check_ids))
            $topelements_ids[]=$topelements_s->id;

            $fill_for_dupl_check_ids[]=$topelements_s->id;

        }

        //add to playlist top 50

        
        $replaceplaylisttrack_resp=SpotifyHelper::instance()->replacePlaylistTracks($managerid,$playlistspid,$topelements_ids);


        if($replaceplaylisttrack_resp)
        {
        echo 'Playlist elements updated successfully';
            DB::table('spotify_accounts_auth_realplaylists')
            ->where('spid', '=', $playlistspid)
            ->where('type', '=','artistplaylist')
            ->update(['artistplayliststate' => '1']);


            DB::table('spotify_accounts_auth')
                      ->where('id', '=', $managerid)
                      ->increment('playlistcount', 1);

              //getting tracks
              $gettracksfromplaylists=SpotifyHelper::instance()->getTracksFromPlaylists($managerid,$playlistspid);



        }
        else
        {
        echo 'There were some problems with adding elements to the playlist.';

            DB::table('spotify_accounts_auth_realplaylists')
                                    ->where('spid', '=', $playlistspid)
                                    ->where('type', '=','artistplaylist')
									->update(['artistplayliststate' => '10']);

        }


        return $replaceplaylisttrack_resp;


        

    }


    

}
