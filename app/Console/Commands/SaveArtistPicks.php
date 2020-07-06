<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use App\Helpers\UserAgentHelper;

class SaveArtistPicks extends Command
{
    
    private $theresultset;
    private $spotifygooglekey;
    private $spotifygetloginurl;
    private $spotifypostloginurl;
    private $spotifycontinueurl;
    private $spotifymainurl;
    private $correction;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:saveartistpicks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save Artist Picks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->spotifygooglekey='6Lfdz4QUAAAAABK1wbAdKww1AEvuJuCTVHoWvX8S';

        $this->spotifygetloginurl = "https://accounts.spotify.com/en/login"; 

        $this->spotifypostloginurl = "https://accounts.spotify.com/login/password"; 

        $this->spotifycontinueurl = "https://accounts.spotify.com/en/status"; 

        $this->spotifymainurl = "accounts.spotify.com";

        $this->correction=100;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        ini_set('max_execution_time', 600); // 600 = 10 minutes

        $checkIfTurnedOn = DB::table('spotify_cron_setter')
        ->where('name', '=', 'saveartistpicks')
        ->first();


        if($checkIfTurnedOn->id!='' && $checkIfTurnedOn->id>0)
        {
            if($checkIfTurnedOn->state!='1')
            {
            echo 'Process is turned off.';
            return;
            }
        }
        else
        {
            echo 'No setting found.';
            return;

        }


    /**
     * Artist needs to be changed: artistpickstate 0=done,check timestamp ;  1=waiting,change spotify ; 2=getfromspotify ; 10=problem
     *
     */
   

        
    $this->theresultset=DB::table('spotify_accounts_auth as t1')
                    ->select('t1.*','t3.active AS artistactive','t3.artistpick','t3.artistpickstate','t3.artisttimestamp','t3.spid AS artistid','t3.backgroundImageUrl')
                     ->leftJoin('spotify_accounts_auth_realartists AS t3', function($join)
							{
                            $join->on('t1.artistconnectid', '=', 't3.id');
                            })
                    ->where('t1.thingstr', '!=', '')
                    ->where('t1.active', '=', '1')
                    ->where('t1.state', '=', '1')
                    ->where('t1.userid', '!=', '0')
                    //->where('t3.artistpickstate', '!=', '10')
                    ->where('t3.active', '=', '1')
					->orderByRaw('t1.id ASC')
                    ->get();
                    
                    
                    foreach($this->theresultset as $theresultset_s)
								{
                                   
                                    echo '...STARTING FOR:...'.$theresultset_s->id.' , artist:'.$theresultset_s->artistid."\n";

                                    
                                    if($theresultset_s->useragent!='')
                                    {
                                        $myUserAgent = $theresultset_s->useragent;
                                    }
                                    else
                                    {
                                        $myUserAgent = UserAgentHelper::instance()->generate();

                                        DB::table('spotify_accounts_auth')
                                        ->where('id',$theresultset_s->id)
                                        ->update(['useragent' => $myUserAgent]);   
                                        
                                    }

                                        if($theresultset_s->artistpickstate=='2') //requested to get spotifyartistpick
                                        {
                                           
                                            echo '...getting from spotify because it was requested...'."\n";

                                            $bearertoken=$this->checkBearerToken($theresultset_s,$this->correction,$myUserAgent);
                                        $this->getFromSpotify($bearertoken,$theresultset_s->id,$theresultset_s->artistid,$myUserAgent);
                                        
                                        }
                                        else
                                        {
                                            if($theresultset_s->artistpickstate=='1') //requested to set spotifyartistpick
                                            echo '...refreshing spotify because it was requested...'."\n";
                                            elseif((Carbon::now()->timestamp-($theresultset_s->artisttimestamp+$this->correction))>3600*24*7)
                                            echo '...refreshing spotify because it is time for artistpickrefresh...'."\n";

                                            if(($theresultset_s->artistpickstate=='1') || ((Carbon::now()->timestamp-($theresultset_s->artisttimestamp+$this->correction))>3600*24*7))    
                                            {
                                            $bearertoken=$this->checkBearerToken($theresultset_s,$this->correction,$myUserAgent);
                                            $this->changeArtistPick($bearertoken,$theresultset_s->id,$theresultset_s->artistid,$theresultset_s->artistpick,$theresultset_s->backgroundImageUrl,$myUserAgent);
                                            }
                                        }
                                    
                                  
                                        echo '...FINISING FOR:...'.$theresultset_s->id.' , artist:'.$theresultset_s->artistid."\n";

                                }

		

     }



    public function getFromSpotify($bearertoken,$managerid,$artistid,$myUserAgent)
    {


        $headers=array(
            "authorization: Bearer ".$bearertoken,
            "Content-Type: application/json"
        );

        $url='https://generic.wg.spotify.com/artist-identity-view/v2/profile/'.$artistid.'?fields=pinnedItem&application=s4a';

        $result=Helperfunctions::instance()->getPageCurl($url,$headers,'','',0,$myUserAgent);

        $json_result = json_decode($result, true);

        $newartistpick='';
        $newbgimage='';
        $newartistpick=$json_result['pinnedItem']['uri'];
        $newbgimageurl=$json_result['pinnedItem']['backgroundImage'];

        if($newartistpick=='')
        {

            //just notification
            echo 'There is nothing to get for manager:'.$managerid.' , artist:'.$artistid;
           

        }

        
        DB::table('spotify_accounts_auth_realartists')
            ->where('spid',$artistid)
            ->update(['artistpickstate' => '0',
                    'artistpick'=>$newartistpick,
                    'backgroundImageUrl'=>$newbgimageurl,
                    'artisttimestamp'=>Carbon::now()->timestamp]);


            echo '...successfully got artist pick from spotify...'."\n";



    }

    public function renewBearerToken($cookies,$managerid,$artistid,$manageremail,$managerpass,$myUserAgent)
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

            echo 'Problem with getting bearer token for manager, getting new cookies :'.$managerid.' , artist:'.$artistid;
            
            DB::table('spotify_accounts_auth')
            ->where('id',$managerid)
            ->update(['cookies' => '']);
            $this->getCookies($managerid,$manageremail,$managerpass,$artistid,$myUserAgent);

        }



        if($bearertoken=='')
        {
            //problem
            DB::table('spotify_accounts_auth_realartists')
            ->where('spid',$artistid)
            ->update(['artistpickstate' => '10']);
                      

            echo 'Problem with getting bearer token still after trying to get new cookies for manager :'.$managerid.' , artist:'.$artistid."\n";
            return;


        }

        //update bearertoken
        DB::table('spotify_accounts_auth')
                    ->where('id',$managerid)
                    ->update(['bearertoken' => $bearertoken,
                              'bearertimestamp' => Carbon::now()->timestamp]);
                    
        //update bearertoken

        return $bearertoken;
    }

    public function deleteArtistPick($bearertoken,$managerid,$artistid,$myUserAgent)
    {

        $data=json_encode(array(
        ));

        $headers=array(
            "authorization: Bearer ".$bearertoken,
            "Content-Type: application/json"
        );

        $del_url='https://generic.wg.spotify.com/artist-identity-view/v1/profile/'.$artistid.'/pinned?organizationUri=spotify:artist:'.$artistid.'?organizationUri=spotify:artist:'.$artistid;
    
        $result=Helperfunctions::instance()->simplePost($del_url,'DELETE',$data,$headers,'','',0,0,0,'','',$myUserAgent);

        if(!$result)
        {

            //problem
            DB::table('spotify_accounts_auth_realartists')
                    ->where('spid',$artistid)
                    ->update(['artistpickstate' => '10']);

            

            echo 'Problem with deleting artist pick for manager:'.$managerid.' , artist:'.$artistid."\n";
            return;
        }
        
    
        DB::table('spotify_accounts_auth_realartists')
                    ->where('spid',$artistid)
                    ->update(['artistpickstate' => '0']);

                    echo '...deleted artist pick at spotify....'."\n";

    }

    public function changeArtistPick($bearertoken,$managerid,$artistid,$artistpick,$backgroundImageUrl,$myUserAgent)
    {
 
        if($artistpick=='')
        return $this->deleteArtistPick($bearertoken,$managerid,$artistid,$myUserAgent);
       

        $checkitem=array();
        $checkitem=SpotifyHelper::instance()->getSpotifyItemId($artistpick);

			if(!in_array($checkitem['type'],array('playlist','album','track','show','episode')) || $checkitem['id']=='')
			{
                $checkitem=array();
				$checkitem=SpotifyHelper::instance()->checkSpotifyItemId($artistpick,$managerid);
                    if(!in_array($checkitem['type'],array('playlist','album','track','show','episode')) || $checkitem['id']=='')
                    {
                        //problem
                            DB::table('spotify_accounts_auth_realartists')
                            ->where('spid',$artistid)
                            ->update(['artistpickstate' => '10']);

                            echo 'Problem with the artistpicklink for manager:'.$managerid.' , artist:'.$artistid."\n";
                            return;
                    }

			}
			
           
            $uri='spotify:'.$checkitem['type'].':'.$checkitem['id'];
            print_r($uri)."\n";
        $data=json_encode(array(
            'uri'=>$uri,
            'type'=>$checkitem['type'],
            'backgroundImageUrl'=>$backgroundImageUrl
        ));
        
        $headers=array(
            "authorization: Bearer ".$bearertoken,
            "Content-Type: application/json"
        );

        $url='https://generic.wg.spotify.com/artist-identity-view/v1/profile/'.$artistid.'/pinned?organizationUri=spotify:artist:'.$artistid;

        

        $result=Helperfunctions::instance()->simplePost($url,'PUT',$data,$headers,'','',0,0,0,'','',$myUserAgent);





        if(!$result)
        {

            //problem
            DB::table('spotify_accounts_auth_realartists')
                    ->where('spid',$artistid)
                    ->update(['artistpickstate' => '10']);

            

            echo 'Problem with changing artist pick for manager:'.$managerid.' , artist:'.$artistid."\n";
            return;
        }
        else
        {

        DB::table('spotify_accounts_auth_realartists')
                    ->where('spid',$artistid)
                    ->update(['artistpickstate' => '0',
                              'artisttimestamp'=>Carbon::now()->timestamp]);


                    echo '...successfully changed artist pick at spotify...'."\n";
        }
        
    }

    public function getCookies($managerid,$manageremail,$managerpass,$artistid,$myUserAgent)
    {

        $try=true;
        $cookietry=0;
        while($try)
        {

     //firstrequest
     $first_cookies = exec(config('myconfig.phantom_js_location').' /storage/spotifyartistlogin/phantomjs/getcookie.js'); //for login
     //firstrequest

     $csrf_token=Helperfunctions::instance()->searchInBetween(';csrf_token=',';__S',$first_cookies);
    
     $gRecaptchaResponse=Helperfunctions::instance()->getPageCaptcha(config('myconfig.solve_recaptcha.key'), $this->spotifygetloginurl, $this->spotifygooglekey);
     //print_r($gRecaptchaResponse);
     
     

     if($managerpass=='')
     {
        //problem
        DB::table('spotify_accounts_auth_realartists')
				->where('spid',$artistid)
				->update(['artistpickstate' => '10']);

        

        echo 'Password is empty:'.$managerid.' , artist:'.$artistid;
        return;
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

     $ch=Helperfunctions::instance()->simplePost($this->spotifypostloginurl,'POST',$builder,$headers,$first_cookies,'',1,0,1,'','',$myUserAgent);
     $cookies=Helperfunctions::instance()->getCookiesFromCurl($ch);
     
    if($cookies['sp_dc']=='')
    {

        //problem
        

        echo 'Problem with getting cookies for manager, trying again:'.$managerid.' , artist:'.$artistid."\n";

        if($cookietry=='5')
        {

         echo 'After 5 tries, unable to get cookies:'.$managerid.' , artist:'.$artistid."\n";

        DB::table('spotify_accounts_auth_realartists')
				->where('spid',$artistid)
				->update(['artistpickstate' => '10']);
                return;
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
				->update(['cookies' => $cookiesstr]);

        //save to db


        return $cookiesstr;

    }
    

    public function checkBearerToken($theresultset_s,$correction,$myUserAgent)
    {
      

        if((Carbon::now()->timestamp-($theresultset_s->bearertimestamp+$correction))>3600 || $theresultset_s->bearertoken=='')
                                        {

                                            if($theresultset_s->cookies!='')
                                            {
                                                echo '...got cookies so getting bearer token...'."\n";
                                                $bearertoken=$this->renewBearerToken($theresultset_s->cookies,$theresultset_s->id,$theresultset_s->artistid,$theresultset_s->email,$theresultset_s->thingstr,$myUserAgent);
                                   
                                            }
                                            else
                                            {
                                                echo '...getting cookies...'."\n";
                                                $cookies=$this->getCookies($theresultset_s->id,$theresultset_s->email,$theresultset_s->thingstr,$theresultset_s->artistid,$myUserAgent);
                                                echo '...getting bearer token...'."\n";
                                                $bearertoken=$this->renewBearerToken($cookies,$theresultset_s->id,$theresultset_s->artistid,$theresultset_s->email,$theresultset_s->thingstr,$myUserAgent);
                                               
                                            }


                                        }
                                        else  // artistpickstate=0 and bearer token is good
                                        {
                                            echo '...got bearer token...'."\n";
                                            $bearertoken=$theresultset_s->bearertoken;

                                        }

            return $bearertoken;

    }

   


}
