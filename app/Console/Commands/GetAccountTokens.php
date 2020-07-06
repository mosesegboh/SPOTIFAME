<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use App\Helpers\UserAgentHelper;

class GetAccountTokens extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:getaccounttokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Account Tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
      
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


    $scopes_s_str='';
    $delim='';
    foreach ($this->scopes as $scopes_s)
    {

        $scopes_s_str.=$delim.$scopes_s;
        $delim=' ';
    }
  

                    $row_get = DB::table('spotify_accounts_auth')
                    ->where('active', '=', '1')
                    ->where('state', '=', '0')
                    ->where('thingstr','!=', '')
                    ->whereNotNull('thingstr')
                        ->get();
      
                        $results=array();

						foreach ($row_get as $row) {

							$results[]=$row;

                        }


                foreach  ($results as $results_s)
                   {

                    if($results_s->useragent!='')
      $myUserAgent = $results_s->useragent;
                    else
      $myUserAgent = UserAgentHelper::instance()->generate();

    $result=SpotifyHelper::instance()->getGrantAccessToAccount($results_s->id,$results_s->email,$results_s->thingstr,$results_s->cookies,$scopes_s_str,$myUserAgent);
   

if($result=='good')
{

    DB::table('spotify_accounts_auth')
				->where('id',$results_s->id)
                ->update(['state' => '1',
                          'scopes' => $scopes_s_str]);
}
else
{

    //problem
    DB::table('spotify_accounts_auth')
				->where('id',$results_s->id)
				->update(['state' => '10']);
}





                   }




    }

   


}
