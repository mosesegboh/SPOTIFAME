<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use App\Helpers\UserAgentHelper;

class FillGenrePlaylists extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:fillgenreplaylists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill Genre Playlists';

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

        ini_set('memory_limit', '512M');
     

        $row_get = DB::table('spotify_genres AS t1')
                        ->select('t1.*','t2.spid AS playlistitemid','t2.managerid AS playlistmanagerid')
                        ->leftJoin('spotify_accounts_auth_realplaylists AS t2', function($join)
							{
                            $join->on('t1.playlistid', '=', 't2.id')
                            ->where('t2.type', '=', 'genreplaylist');
                            })
                        ->where('t1.item_count','>','0')
                        //->where('t2.genreplayliststate','=','11')
                        /*->where(function ($query) {
                            $query->where('t2.genreplayliststate', '=', '0')
                                ->orWhereNull('t2.genreplayliststate');
                        })
                        */
                        ->whereNull('t2.genreplayliststate')
                        ->whereNotNull('t1.playlistid')
                        ->orderByRaw('t1.item_count DESC')
                        ->limit(1)
						->get();

                        $results_get=array();
                        
						foreach ($row_get as $row_get_s) {

							$results_get[]=$row_get_s;

                        }

                        if(empty($results_get))
                        {
                            echo 'no more genres, quitting';
                            exit;
                        }
            
                    //print_r($results_get);

                

                    foreach ($results_get as $results_get_s)
                    {


                        echo '...Putting tracks to playlist process started...'."\n"; 
          $puttop_response=$this->putTopGenreTracks($results_get_s->id,$results_get_s->item_count,$results_get_s->playlistmanagerid,$results_get_s->playlistitemid);

                                if($puttop_response)
                                  {

           // $this->putThePlaylistToArtistPage($main_sp_account_id,$theplaylist->id,$artistid,$myUserAgent);

                                  }



                    }




    }



    public function putTopGenreTracks($genreid,$genrecount,$managerid,$playlistspid)
    {


echo '...setting state to zero...'."\n"; 

echo '...genre id is: '.$genreid."\n"; 
echo '...managerid is: '.$managerid."\n";
echo '...playlistid is: '.$playlistspid."\n";

        DB::table('spotify_accounts_auth_realplaylists')
                            ->where('spid', '=', $playlistspid)
                            ->where('type', '=','genreplaylist')
                            ->update(['genreplayliststate' => '0']);


        $row_get = DB::table('spotify_itemgenre_fk AS t1')
        ->select('t1.item_id')
        ->where('t1.genre_id', '=', $genreid)
        ->get();

        $results_get=array();
        foreach ($row_get as $row_get_s) {
            $genre_ids[]=$row_get_s->item_id;
        }

        //if($genrecount<10)
       // $correction=10;
        //else
        $correction=0;
        $numberoftracks=rand(100,200);

        $limit=min(200,$genrecount);
        //echo $limit;exit;

echo '...number of track will be maximum:'.$numberoftracks.'...'."\n"; 

        $artists=DB::table('spotify_items AS t1')
					->select('t1.*')
					->where(function($query) use ($genre_ids)
							{
								if (!empty($genre_ids)) {
									$query->whereIn('t1.id',$genre_ids);
								}
                            })
                    ->where('t1.type', '=', 'artist')
                    ->orderByRaw('t1.followercount DESC')
                    ->limit($limit)
                    ->offset(0)
                    ->get();

                    $spotify_desc_limit=360;
                    $desc_char_count=0;
        $playlistdescription=array();  
        $thetracklist=array();
        $alltoptracks=array();
        $count_items=0;
        
                    foreach ($artists as $key => $artist) {
                        
 $spotifyapi=SpotifyHelper::instance()->getSpotifySearchTokens();

$toptracks= SpotifyHelper::instance()->getArtistsTopTracks( $spotifyapi,$artist->itemid)->tracks;
if(empty($toptracks))
{
    unset($artists[$key]);
continue;
}

//echo $artist->id."\n";
$alltoptracks[$artist->id]=$toptracks;

//echo $alltoptracks[$artist->id][0]->id;
//echo $artist->id."\n";
//echo $toptracks[1]->id."\n";

if($count_items<'15')
{

$desc_char_count=$desc_char_count+mb_strlen($toptracks[0]->album->artists[0]->name,'UTF-8');
if($desc_char_count<$spotify_desc_limit)
$playlistdescription[]=$toptracks[0]->album->artists[0]->name;

array_push($thetracklist, $toptracks[0]->id);
$lastpushedartistid=$artist->id;

unset($alltoptracks[$artist->id][0]);

if(empty($alltoptracks[$artist->id]))
    {
unset($alltoptracks[$artist->id]);
unset($artists[$key]);

    }
else
    {
$alltoptracks[$artist->id]=array_values($alltoptracks[$artist->id]);
    }

    $count_items++;
}




                    }
             

                    
                
                   // echo count($artists);exit;
        
                   if(empty($artists))
                   {
            echo 'artists empty, quitting';
                    exit;
                   }

                   
                   //reorder artists to array
                   
                   $c=0;
                   foreach ($artists as $key => $artist)
                   {
                    $new_artists[$c]=$artist;
                    
                    $c++;
                   }

                   $artists=$new_artists;
                   
                   //reorder artists to array
/*
                   foreach ($new_artists as $key => $artist)
                   {
   echo $artist->id."\n";

                   }
exit;
*/
                if(empty($artists))
                   {
            echo 'artists empty, quitting';
                    exit;
                   }
                        if($genrecount=='1' || count($alltoptracks)=='1')
                        {
            
                            foreach ($artists as $artist) {


                                foreach ($alltoptracks[$artist->id] as $singleartistpieces)
                                {
                                    if($singleartistpieces->id!='')
                                    array_push($thetracklist, $singleartistpieces->id);


                                    $count_items++;
                                    if($count_items>=$numberoftracks)
                                    break 2;

                                }


                            }

                            


                        }
                else
                        {



                            $try=true;
                            while($try)
                            {

                                

                           
                           $randomartist_index = array_rand($artists);

                           $therandomartistid=$artists[$randomartist_index]->id;

                                if($therandomartistid==$lastpushedartistid && count($artists)=='1')
                                $try=false;

                           if($therandomartistid==$lastpushedartistid)  // can not be last pushed
                            continue;

                          // echo 'actor:'.$alltoptracks[$therandomartistid][0]->album->artists[0]->name."\n";

                
                        $desc_char_count=$desc_char_count+mb_strlen($alltoptracks[$therandomartistid][0]->album->artists[0]->name,'UTF-8');
                            if($desc_char_count<$spotify_desc_limit)
                          $playlistdescription[]=$alltoptracks[$therandomartistid][0]->album->artists[0]->name;
                            array_push($thetracklist, $alltoptracks[$therandomartistid][0]->id);

                            $lastpushedartistid=$therandomartistid;
                            unset($alltoptracks[$therandomartistid][0]);

                        
                            if(empty($alltoptracks[$therandomartistid]))
                            {
                                unset($alltoptracks[$therandomartistid]);
                                unset($artists[$randomartist_index]);
                                
                            }
                            else
                            {
                                $alltoptracks[$therandomartistid]=array_values($alltoptracks[$therandomartistid]);
                            }
                            

                                
                            $count_items++;


                                if(empty($artists))
                                $try=false;

                                if($count_items>=$numberoftracks)
                                $try=false;


                            }


                        }
               

$thetracklist=array_unique($thetracklist);
$thetracklist=array_values($thetracklist);
//print_r($thetracklist);
//print_r(count($thetracklist));

$playlistdescription_str=implode(", ", array_filter(array_unique($playlistdescription)));


$first100=array_slice($thetracklist, 0, 100);
$second100=array_slice($thetracklist, 100);

echo '...replacing tracks...'."\n";
$replaceplaylisttrack_resp=SpotifyHelper::instance()->replacePlaylistTracks($managerid,$playlistspid,$first100);

$getthetracks=0;
if($replaceplaylisttrack_resp)
        {
        echo 'First 100 playlist elements updated successfully'."\n";

            $getthetracks=1; //if adding was successfull
        }
        else
        {
            echo 'There were some problems with adding first 100 elements to the playlist: '.$playlistspid."\n";

            DB::table('spotify_accounts_auth_realplaylists')
                                    ->where('spid', '=', $playlistspid)
                                    ->where('type', '=','genreplaylist')
                                    ->update(['genreplayliststate' => '10']);
                                    exit;

        }
      
if(!empty($second100))
{
$replaceplaylisttrack_resp2=SpotifyHelper::instance()->addPlaylistTracks($managerid,$playlistspid,$second100);      

        if($replaceplaylisttrack_resp2)
        {
        echo 'Second 100 playlist elements added successfully'."\n";
        }
        else
        {
        echo 'There were some problems with adding second 100 elements to the playlist: '.$playlistspid."\n";

            DB::table('spotify_accounts_auth_realplaylists')
                                    ->where('spid', '=', $playlistspid)
                                    ->where('type', '=','genreplaylist')
                                    ->update(['genreplayliststate' => '10']);
                                    exit;

        }

}

//getting tracks
$gettracksfromplaylists=SpotifyHelper::instance()->getTracksFromPlaylists($managerid,$playlistspid);

echo '...adding description...'."\n";


$playlistoptions=array('description'=>$playlistdescription_str);
$pl_update=SpotifyHelper::instance()->updatePlaylist($managerid,$playlistspid,$playlistoptions);

if($pl_update)
        {
        echo 'Description set successfully'."\n";
        }
        else
        {
        echo 'There were some problems with setting description for playlist: '.$playlistspid."\n";

            DB::table('spotify_accounts_auth_realplaylists')
                                    ->where('spid', '=', $playlistspid)
                                    ->where('type', '=','genreplaylist')
                                    ->update(['genreplayliststate' => '10']);
                                    exit;

        }



        echo '...updating playlist in our database...'."\n";

        DB::table('spotify_items')
		->where('itemid', '=', $playlistspid)
		->update([
            'description'=>$playlistdescription_str,
            'timestamp' => Carbon::now()->timestamp,
        ]);



        DB::table('spotify_accounts_auth_realplaylists')
        ->where('spid', '=', $playlistspid)
        ->where('type', '=','genreplaylist')
        ->update(['genreplayliststate' => '1']);


        echo '...succesful playlist fill...'."\n";
    }

    

}
