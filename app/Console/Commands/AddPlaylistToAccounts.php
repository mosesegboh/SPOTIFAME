<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use App\Helpers\UserAgentHelper;

class AddPlaylistToAccounts extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:addplaylisttoaccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Playlist To Generated Accounts';

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

    
        $useridtoinsertto=0; //these accounts are artist playlist accounts, owned by nobody!


        $row_get = DB::table('spotify_accounts_auth')
                        ->where('active', '=', '1')
                        ->where('state', '=', '1')
                        ->where('userid','=', $useridtoinsertto)
						->orderByRaw('id ASC')
						->get();

						$results=array();

						foreach ($row_get as $row) {

							$results[]=$row;

                        }
                        
                   foreach  ($results as $results_s)
                   {

                    $pl_count=0;
                    $pl_count = DB::table('spotify_accounts_auth_realplaylists')
                    ->where('userid', '=', $useridtoinsertto)
                    ->where('managerid', '=', $results_s->id)
                    ->count();

        
        while ($pl_count<8)
                        {


                            $iconresult=$this->generateIconString();

                    
          $myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($results_s->id);       


        $row_get_genres = DB::table('spotify_genres')
                        ->whereNull('playlistid')
                        ->orderByRaw('item_count DESC')
                        ->limit(1)
						->get();

                        $results_genres=array();
                        
						foreach ($row_get_genres as $row_genres) {

							$results_genres[]=$row_genres;

                        }

                        if(empty($results_genres))
                        {
                            echo 'no more genres left';exit;
                        }
                        echo $results_genres[0]->id;

        if(rand(1,2)=='1')
        $finalname= $iconresult.ucwords($results_genres[0]->name);
          else
        $finalname= ucwords($results_genres[0]->name).$iconresult;

        $myrealplaylists_s='';

        $try=true;
          while($try)
          {
            try {

                        $myrealplaylists_s=$myspotifyapi->createPlaylist([
                            'name' => $finalname
                        ]);
                        $try=false;
                
            } catch (\Exception $e) {

                        if ($e->getCode() == 429) {
                            echo 'Rate limit, trying again:'.$results_s->id."\n";

                        }
                        else
                        {
                        echo 'spotify account is disconnected either by user or spotify'.$e->getCode();
                        DB::table('spotify_accounts_auth')
                    ->where('id', $results_s->id)
                    ->update(['state' => '10']);
                        $try=false;
                        }


                }

            }

            if((empty($myrealplaylists_s)) || $myrealplaylists_s->id=='')
            {
                echo 'Playlist creation failed, trying again';
                exit;
            }

        DB::table('spotify_accounts_auth_realplaylists')
					->insert(
				[
                'spid' => $myrealplaylists_s->id,
				'dt' => Carbon::now(),
				'userid'=>$useridtoinsertto,
                'managerid'=>$results_s->id,
                'type'=>'genreplaylist']
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



        DB::table('spotify_genres')
				->where('id',$results_genres[0]->id)
                ->update(['playlistid' => $playlistid]);


                DB::table('spotify_accounts_auth')
                ->where('id', '=', $results_s->id)
                ->increment('playlistcount', 1);
                



       // print_r($myrealplaylists_s);exit;
         
               
       $pl_count++;

       sleep(1);
                            }
                

                    }
                






    }


    public function generateIconString()
    {

        $random_pl_names=array('🎼',
        '🎵','🎶', '🎙','🎚','🎛',
        '🎤','🎧','📻','🎸',
        '🎹','🎺','🎻','🥁',
        '🔈','🔉','🔊','📀','💿',
        '💽','💃','🕺');
        

        $amountoficons=rand(1,3);

        $figure1=$random_pl_names[rand(0,count($random_pl_names)-1)];
        $random_pl_names=array_diff( $random_pl_names, array($figure1) );
        sort($random_pl_names);

        $figure2=$random_pl_names[rand(0,count($random_pl_names)-1)];
        $random_pl_names=array_diff( $random_pl_names, [$figure2] );
        sort($random_pl_names);

        $figure3=$random_pl_names[rand(0,count($random_pl_names)-1)];
        $random_pl_names=array_diff( $random_pl_names, [$figure3] );
        sort($random_pl_names);

        $iconresult='';
        if($amountoficons=='1')
        $iconresult.=$figure1;
        elseif($amountoficons=='2')
        $iconresult.=$figure1.$figure2;
        elseif($amountoficons=='3')
        $iconresult.=$figure1.$figure2.$figure3;

        return $iconresult;

    }

   


}
