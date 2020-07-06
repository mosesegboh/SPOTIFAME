<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

class HomePageActiveGenresChange extends Command
{
    
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:homepageactivegenreschange';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Active Home Page Genres';

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

        $theresultset=DB::table('spotify_statistics_home_genres as t1')
                    ->select('t1.*')
                    ->where('active','=','1')
					->limit(1)
                    ->get();

                    foreach ($theresultset as $theresultset_s)
                    {

                   $currentactive=$theresultset_s->id;

                    }

                    if($currentactive=='20')
                    $nextactive=1;
                    else
                    $nextactive=$currentactive+1;
               
            DB::table('spotify_statistics_home_genres')
            ->where('id','=',$currentactive)
            ->update([
                'active' => '0',
            ]);


            DB::table('spotify_statistics_home_genres')
            ->where('id','=',$nextactive)
            ->update([
                'active' => '1',
            ]);
     



     }




}
