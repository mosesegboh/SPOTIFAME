<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

class GetFourLetterWords extends Command
{
    
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:getfourletterwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Four Letter Words';

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


        //$pages=file_get_contents('https://word.tips/four-letter-words/?dictionary=all_en&length=4&page=1&result_sort=alphabet');
        $pages=file_get_contents('https://word.tips/three-letter-words/?dictionary=all_en&length=3&page=1&result_sort=alphabet');

        $thematches=Helperfunctions::instance()->searchInBetween('<div class="card card-word-wrapper word_page word-length  mb-2"','<div class="card-footer text-muted pagination-footer">',$pages);
        
//$all_expl=explode("data-length='4' data", $pages);
$all_expl=explode("data-length='3' data", $pages);

$i=0;
foreach ($all_expl as $all_expl_s)
{

$singles[]=Helperfunctions::instance()->searchInBetween("-word='","'>",$all_expl_s);

$i++;
}
        ///print_r(array_filter($singles));

        $singles=array_filter($singles);

        $search_string='';
        $searchtype='artist';
        //$searchtype='playlist';
        $searchgenre='';
        $market='';

        if($searchtype=='artist')
        $yearfromto=' year:1900-2020';
                
        foreach ($singles as $singles_s)
        {
            
            $search_string=str_replace('_','',$searchtype).
				'_'.str_replace('_','',trim($singles_s)).
				'_'.str_replace('_','',$yearfromto).
				'_'.str_replace('_','',$market).
				'_'.str_replace('_','',trim($searchgenre)).
				'_'.
                '_';

                
            DB::table('spotify_search_cache')
						->updateOrInsert(
					['searchstring' => base64_encode($search_string)],
					['item_count'=> 0,
					'userid'=> '0',
					'dt' => Carbon::now(),
					'inprogress'=> '1']
							);

                            $last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_search_cache')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}
            
            

        }


    }



}
