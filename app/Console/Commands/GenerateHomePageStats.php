<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

class GenerateHomePageStats extends Command
{
    
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:generatehomepagestats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Home Page Statistics';

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

        $theresultset=DB::table('spotify_statistics_homepage as t1')
                    ->select('t1.*')
					->orderByRaw('t1.id ASC')
                    ->get();

                    foreach ($theresultset as $theresultset_s)
                    {

                        $statistics[$theresultset_s->realname]=$theresultset_s->realvalue;

                    }

             //print_r($statistics);

                //playlistscount
             $itemcount=array();
         $itemcount= DB::table('spotify_statistics')
             ->where('realname', '=', 'allplaylists')
             ->get();

             $statistics['allplaylists']=0;
         foreach ($itemcount as $itemcount_s)
         {
            $statistics['playlistscount']=$itemcount_s->realvalue/10;
         }

            DB::table('spotify_statistics_homepage')
            ->where('realname','=','playlistscount')
            ->update([
                'realvalue' => $statistics['playlistscount'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);


                //consolidatedfollowers
                // Justin Bieber   1uNFoZAHBGtllmzznpCI3s
                // Ariana Grande   66CXWjxzNUsdJxJ2JdwvnR
                // Metallica       2ye2Wgw4gimLv2eAKyk1NB
                $spotifyapi=SpotifyHelper::instance()->getSpotifySearchTokens();


                $justin_bieber=SpotifyHelper::instance()->getArtist($spotifyapi,'1uNFoZAHBGtllmzznpCI3s');
                $ariana_grande=SpotifyHelper::instance()->getArtist($spotifyapi,'66CXWjxzNUsdJxJ2JdwvnR');
                $metallica=SpotifyHelper::instance()->getArtist($spotifyapi,'2ye2Wgw4gimLv2eAKyk1NB');
                

                $statistics['consolidatedfollowers']=$justin_bieber->followers->total+$ariana_grande->followers->total+$metallica->followers->total;
   
               DB::table('spotify_statistics_homepage')
               ->where('realname','=','consolidatedfollowers')
               ->update([
                   'realvalue' => $statistics['consolidatedfollowers'],
                   'dt'=>Carbon::now()->format('Y-m-d H:i:s')
               ]);


                //trackpromotions
            DB::table('spotify_statistics_homepage')
            ->where('realname','=','trackpromotions')
            ->update([
                'realvalue' => rand(12000,24000),
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);
     

           
                //genrecharts
          DB::table('spotify_statistics_home_genres')->truncate();

            $genresset=DB::table('spotify_genres as t1')
                    ->select('t1.*')
                    ->where('item_count','>','100')
                    ->orderByRaw('RAND()')
                    ->offset(0)
                    ->limit(60)
                    ->get();

                $counter=0;
                $active=1;
                $variation=array();
                    foreach ($genresset as $genresset_s)
                    {
                        $counter++;

                        $variation[]=$genresset_s->id;

                        
                        if($counter == 3)
                        {
                            $variation_str=implode("|",$variation);
                       
                            
                            DB::table('spotify_statistics_home_genres')->insert(
                                ['variation' => $variation_str,
                                'active'=>$active,
                                'dt' => Carbon::now(),
                                'timestamp' => Carbon::now()->timestamp
                                ]
                            );

                            $active=0;
                            $counter=0;
                            $variation=array();
                        }

                        
                    }



     }




}
