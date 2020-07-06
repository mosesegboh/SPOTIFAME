<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use App\Helpers\UserAgentHelper;

use Illuminate\Support\Facades\Crypt;

use Carbon\Carbon;

class AddRandomUsers extends Command
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
    protected $signature = 'cron:addrandomusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Random Users';

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
        
       
        ini_set('max_execution_time', 600); // 600 = 10 minutes

        $checkIfTurnedOn = DB::table('spotify_cron_setter')
        ->where('name', '=', 'addrandomusers')
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

            //get last inserted generated
            $lastinsertedresultset=DB::table('spotify_accounts_auth as t1')
            ->select('t1.*')
            ->where('t1.generated', '=', '1')
            ->where('t1.manuallycreated', '=', '0')
            ->orderByRaw('t1.id DESC')
            ->limit(1)
            ->get();
            //get last inserted generated
            foreach($lastinsertedresultset as $lastinsertedresultset_s)
            {
                $theresult=$lastinsertedresultset_s;
            }

            if(!(Carbon::now()->timestamp-$theresult->timestamp>(60*10)))
			{

                echo '...10 minutes has not passed yet so exiting...';
                exit;

            }

            $lastinserteddomain=explode('@',$theresult->email)[1];

            $reg_d_index = array_search($lastinserteddomain, $this->reg_domains);

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

            $displayname=$uniqueaccount['displayname'];
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
                                'thingstr'=>$generatedpass,
                                'generated'=>'1',
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

    

}
