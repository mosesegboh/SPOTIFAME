<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use App\Helpers\UserAgentHelper;

class GetTracksFromPlaylists extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:gettracksfromplaylists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Tracks From Our Playlists';

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



            $get= DB::table('spotify_accounts_auth_realplaylists AS t1')
					->select('t1.*')
                    ->where('t1.trackgetstate', '=', '0')
                    ->limit(10)
					->get();

            foreach($get as $get_s)
            {

                DB::table('spotify_accounts_auth_realplaylists')
				->where('id', $get_s->id)
				->update(['trackgetstate' => '2']);

                echo '...trying to get tracks for:'.$get_s->spid.'...'."\n";

                $myspotifyapi=SpotifyHelper::instance()->getMySpotifyTokens($get_s->managerid);
                
                $tracks=array();
                $tracks=SpotifyHelper::instance()->getPlaylistTracks($myspotifyapi,$get_s->spid);

                if(empty($tracks))
                {
                echo 'tracks object is empty';
                exit;
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

                         $snapshotid=SpotifyHelper::instance()->deletePlaylistTracks($myspotifyapi,$get_s->spid, $delete_tracksarray,$snapshotid);
                         continue;
                        }

                        SpotifyHelper::instance()->addItemToDB($myspotifyapi,$tracks_s->track->id,'track');

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
                                ['playlist_id' => $get_s->id,
                                'track_id' => $realtrackid],
                                [ 
                                'position' => $track_count
                                    ]
                                         );
                                        

                                        $dupl_check_tracks[]=$tracks_s->track->id;
                    
                                        $track_count++;
                    }

   

                    DB::table('spotify_accounts_auth_realplaylists')
				->where('id', $get_s->id)
				->update(['trackgetstate' => '1']);

              
                






            }

        
                
            





    }

    

}
