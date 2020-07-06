<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use App\Helpers\UserAgentHelper;

class CheckAccountActiveState extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:checkaccountactivestate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Account Active State';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
      
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        ini_set('max_execution_time', 600); // 600 = 10 minutes

        ini_set('max_execution_time', 600); // 600 = 10 minutes

        
        $row_get = DB::table('spotify_accounts_auth')
                        ->where('active', '=', '1')
                        ->where('state', '=', '1')
                        ->where('userid','=', '1')
						->orderByRaw('id ASC')
						->get();

						$results=array();

						foreach ($row_get as $row) {

							$results[]=$row;

                        }
                        
                   foreach  ($results as $results_s)
                   {
                    

          $myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($results_s->id);       

          $myprofile=new \stdClass();

        $try=true;
		while($try)
		{

          try {

            $myprofile=$myspotifyapi->me();
            $try=false;

            } catch (\Exception $e) {

                    if ($e->getCode() == 429) {
                        echo 'Rate limit, trying again:'.$results_s->id."\n";

                    }
                    else
                    {
                        echo 'Exception error: '.$e->getMessage().'spotify account is disconnected either by user or spotify:'.$results_s->id."\n";
                        DB::table('spotify_accounts_auth')
                        ->where('id', $results_s->id)
                        ->update(['state' => '10']);
                        $try=false;
                    }
              
            

                }
        }

        
          if($myprofile->display_name !='')
          {
              echo 'profile good:'.$results_s->id."\n";
              if($results_s->state!='1')
              {
              DB::table('spotify_accounts_auth')
             ->where('id', $results_s->id)
             ->update(['state' => '1']);
             }
              
          }
          else
          {
             echo 'spotify account is disconnected either by user or spotify:'.$results_s->id."\n";

             DB::table('spotify_accounts_auth')
             ->where('id', $results_s->id)
             ->update(['state' => '10']);
          }
          
         // print_r($myprofile)."\n";exit;

                sleep(1);

                    }



    }

   


}
