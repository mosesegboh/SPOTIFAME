<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

class GenerateStats extends Command
{
    
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:generatestats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Statistics';

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

        $theresultset=DB::table('spotify_statistics as t1')
                    ->select('t1.*')
					->orderByRaw('t1.id ASC')
                    ->get();

                    foreach ($theresultset as $theresultset_s)
                    {

                        $statistics[$theresultset_s->realname]=$theresultset_s->realvalue;

                    }

             //print_r($statistics);


             //recaptcha balance
        $balance=Helperfunctions::instance()->getPageCaptchBalance(config('myconfig.solve_recaptcha.key'));
        if($balance->errorId=='0')
        {
            $statistics['recaptchabalance']=$balance->balance;
        }

        DB::table('spotify_statistics')
                ->where('realname','=','recaptchabalance')
                ->update([
                    'realvalue' => $statistics['recaptchabalance'],
                    'dt'=>Carbon::now()->format('Y-m-d H:i:s')
                ]);
                
     
             //genres
            //uniquegenres
      
            $itemcount=array();
            $itemcount= DB::table('spotify_genres')
            ->select(\DB::raw('COUNT(*) as thecount'))
			->get();
            $statistics['uniquegenres']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['uniquegenres']=$itemcount_s->thecount;

            }

            DB::table('spotify_statistics')
                ->where('realname','=','uniquegenres')
                ->update([
                    'realvalue' => $statistics['uniquegenres'],
                    'dt'=>Carbon::now()->format('Y-m-d H:i:s')
                ]);

                $item_per_genre=array();
                $item_per_genre= DB::table('spotify_itemgenre_fk')
                    ->select(\DB::raw('COUNT(item_id) as thecount,genre_id'))
                    ->groupBy('genre_id')
                    ->get();

                    foreach ($item_per_genre as $item_per_genre_s)
                    {
                        
                        DB::table('spotify_genres')
                        ->where('id','=',$item_per_genre_s->genre_id)
                        ->update([
                            'item_count' => $item_per_genre_s->thecount,
                            'dt'=>Carbon::now()->format('Y-m-d H:i:s')
                        ]);
        
                    }

            $itemcount=array();
            $itemcount= DB::table('spotify_items')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where(function ($query) {
                $query->where('genres', '=', '')
                    ->orWhereNull('genres');
            })
            ->where('type', '=', 'artist')
			->get();
            $statistics['nogenreartists']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['nogenreartists']=$itemcount_s->thecount;
            }

                DB::table('spotify_statistics')
                ->where('realname','=','nogenreartists')
                ->update([
                    'realvalue' => $statistics['nogenreartists'],
                    'dt'=>Carbon::now()->format('Y-m-d H:i:s')
                ]);


            $itemcount=array();
            $itemcount= DB::table('spotify_items')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('type', '=', 'artist')
			->get();
            $statistics['allartists']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['allartists']=$itemcount_s->thecount;
            }

                DB::table('spotify_statistics')
                ->where('realname','=','allartists')
                ->update([
                    'realvalue' => $statistics['allartists'],
                    'dt'=>Carbon::now()->format('Y-m-d H:i:s')
                ]);


            $itemcount=array();
            $itemcount= DB::table('spotify_items')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('type', '=', 'playlist')
			->get();
            $statistics['allplaylists']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['allplaylists']=$itemcount_s->thecount;
            }

            DB::table('spotify_statistics')
            ->where('realname','=','allplaylists')
            ->update([
                'realvalue' => $statistics['allplaylists'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);



            $itemcount=array();
            $itemcount= DB::table('spotify_items')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('type', '=', 'artist')
            ->where('claimed', '=', '2')
			->get();
            $statistics['knownunclaimed']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['knownunclaimed']=$itemcount_s->thecount;
            }

            DB::table('spotify_statistics')
            ->where('realname','=','knownunclaimed')
            ->update([
                'realvalue' => $statistics['knownunclaimed'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);
     
            $itemcount=array();
            $itemcount= DB::table('spotify_accounts_auth')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('userid', '!=', '0')
			->get();
            $statistics['connectedaccounts']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['connectedaccounts']=$itemcount_s->thecount;
            }

            DB::table('spotify_statistics')
            ->where('realname','=','connectedaccounts')
            ->update([
                'realvalue' => $statistics['connectedaccounts'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);
     


            $itemcount=array();
            $itemcount= DB::table('spotify_accounts_auth')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('generated', '=', '1')
            ->where('userid', '=', '0')
			->get();
            $statistics['generatedaccounts']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['generatedaccounts']=$itemcount_s->thecount;
            }
            DB::table('spotify_statistics')
            ->where('realname','=','generatedaccounts')
            ->update([
                'realvalue' => $statistics['generatedaccounts'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);

            

            $itemcount=array();
            $itemcount= DB::table('spotify_accounts_auth_realartists')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('userid', '!=', '0')
			->get();
            $statistics['controlledartists']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['controlledartists']=$itemcount_s->thecount;
            }
            DB::table('spotify_statistics')
            ->where('realname','=','controlledartists')
            ->update([
                'realvalue' => $statistics['controlledartists'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);


            $itemcount=array();
            $itemcount= DB::table('spotify_accounts_auth_realplaylists')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('userid', '!=', '0')
			->get();
            $statistics['controlledplaylists']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['controlledplaylists']=$itemcount_s->thecount;
            }

            DB::table('spotify_statistics')
            ->where('realname','=','controlledplaylists')
            ->update([
                'realvalue' => $statistics['controlledplaylists'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $itemcount=array();
            $itemcount= DB::table('spotify_items')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('type', '=', 'artist')
            ->where('timestamp','>',Carbon::today()->timestamp)
			->get();
            $statistics['newartiststoday']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['newartiststoday']=$itemcount_s->thecount;
            }
            DB::table('spotify_statistics')
            ->where('realname','=','newartiststoday')
            ->update([
                'realvalue' => $statistics['newartiststoday'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $itemcount=array();
            $itemcount= DB::table('spotify_items')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('type', '=', 'playlist')
            ->where('timestamp','>',Carbon::today()->timestamp)
			->get();
            $statistics['newplayliststoday']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['newplayliststoday']=$itemcount_s->thecount;
            }
            DB::table('spotify_statistics')
            ->where('realname','=','newplayliststoday')
            ->update([
                'realvalue' => $statistics['newplayliststoday'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $itemcount=array();
            $itemcount= DB::table('spotify_items')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('type', '=', 'artist')
            ->where('timestamp','>',Carbon::now()->startOfWeek()->timestamp)
			->get();
            $statistics['newartiststhisweek']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['newartiststhisweek']=$itemcount_s->thecount;
            }
            DB::table('spotify_statistics')
            ->where('realname','=','newartiststhisweek')
            ->update([
                'realvalue' => $statistics['newartiststhisweek'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $itemcount=array();
            $itemcount= DB::table('spotify_items')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('type', '=', 'playlist')
            ->where('timestamp','>',Carbon::now()->startOfWeek()->timestamp)
			->get();
            $statistics['newplayliststhisweek']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['newplayliststhisweek']=$itemcount_s->thecount;
            }
            DB::table('spotify_statistics')
            ->where('realname','=','newplayliststhisweek')
            ->update([
                'realvalue' => $statistics['newplayliststhisweek'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $itemcount=array();
            $itemcount= DB::table('spotify_accounts_auth')
            ->select(\DB::raw('COUNT(*) as thecount'))
            ->where('userid', '!=', '0')
            ->where('timestamp','>',Carbon::now()->startOfWeek()->timestamp)
			->get();
            $statistics['newconnectionsthisweek']=0;
            foreach ($itemcount as $itemcount_s)
            {
                $statistics['newconnectionsthisweek']=$itemcount_s->thecount;
            }
            DB::table('spotify_statistics')
            ->where('realname','=','newconnectionsthisweek')
            ->update([
                'realvalue' => $statistics['newconnectionsthisweek'],
                'dt'=>Carbon::now()->format('Y-m-d H:i:s')
            ]);
           


            //get generated accounts by country
            $row_get = DB::table('spotify_accounts_auth')
                        ->where('active', '=', '1')
                        ->where('state', '=', '1')
						->groupBy('country')
						->get();

						$results=array();

						foreach ($row_get as $row) {

							$results[]=$row;

                        }
                        
                   foreach  ($results as $results_s)
                   {

                    $row_get_country_count=0;
                    $row_get_country_count = DB::table('spotify_accounts_auth')
                        ->where('country', '=', $results_s->country)
                        ->count();
                        
                    


                            DB::table('spotify_statistics_gen_country')
					->updateOrInsert(
				['country' => $results_s->country],
				[
                'generated' => $results_s->generated,
                'account_count' => $row_get_country_count,
				'dt' => Carbon::now()]
						);
						

                   }

           
                   //get generated accounts by country
            

        

     }




}
