<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

use App\Http\Controllers\Traits\SettingsTrait;

class GetArtistsClaimState extends Command
{
    use SettingsTrait;

   private $claimedshow;
   private $claimedshow2;
   private $notclaimedshow;
   private $unknownshow;

   private $rangeslidervalues=array();
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:getartistsclaimstate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Artists Claim State';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->generateConfig();

        $this->settings=$this->getSettings();


        $this->rangeslidervalues=array(
            $this->settings['sp_range_min'],
            $this->settings['sp_range_max'],
            $this->settings['sp_range_break']

        );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        ini_set('max_execution_time', 600); // 600 = 10 minutes


        $isfulltextsearch=1;
		
		

        $checkIfTurnedOn = DB::table('spotify_cron_setter')
        ->where('name', '=', 'getartistsclaimstate')
        ->first();

        if($checkIfTurnedOn->id!='' && $checkIfTurnedOn->id>0)
        {
            if($checkIfTurnedOn->state!='1')
            {
            echo 'Process is turned off.';
            exit;
            }
        }
        else
        {
            echo 'No setting found.';
            exit;

        }

		$checkIfRunning = DB::table('spotify_artists_claim_queue')
        ->where('inprogress', '=', '2')
        ->first();
        
        if($checkIfRunning->id!='' && $checkIfRunning->id>0)
        {
            echo 'Another process is already running';
            return;
        }


            $getItemToProcess = DB::table('spotify_artists_claim_queue')
            ->where('inprogress', '=', '1')
            ->orderByRaw('id ASC')
            ->first();


            if($getItemToProcess->id!='' && $getItemToProcess->id>0)
            {
                echo 'There are searches to process so starting...';
                DB::table('spotify_artists_claim_queue')
                ->where('id', '=', $getItemToProcess->id)
                ->update(['inprogress' => '2',
                ]);

                
                $claim_queue_id=$getItemToProcess->id;
                $lastid=$getItemToProcess->lastid;
                $item_left_old=$getItemToProcess->item_left;
                $item_count_old=$getItemToProcess->item_count;

                $this->claimedshow=$getItemToProcess->claimedrefresh;
                $this->claimedshow2=$getItemToProcess->claimed2refresh;
                $this->notclaimedshow=$getItemToProcess->notclaimedrefresh;
                $this->unknownshow=$getItemToProcess->unknownrefresh;

                $this->artistswithoutgenres=$getItemToProcess->artistswithoutgenres;
                

            }
            else
            {
                echo 'There are no searches to process...';
                return;
            }




           $thesearchstring=base64_decode($getItemToProcess->searchstring);

           $thesearchstring_expl=explode('_',$thesearchstring);

           $searchtype=$thesearchstring_expl[0];
           $searchname=$thesearchstring_expl[1];

           
           $inputfolmin=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[2],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
           $inputfolmax=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[3],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);

           $searchgenrestring=$thesearchstring_expl[4];

		   if($this->artistswithoutgenres=='1')
           $searchgenrestring='';

           if($searchtype!='artist')
           {

            $this->returnEmpty($claim_queue_id);

        
           }



           $searchgenre=array();
            $genre_ids=array();
            if($searchgenrestring !='')
				{
            $thegenrearray=explode(',',$searchgenrestring);
                    sort(array_filter($thegenrearray));


                    foreach ($thegenrearray as $thegenrearray_s)
					{

						$thegenrearray_s=trim($thegenrearray_s);
				$searchgenre[]=$thegenrearray_s;

				

				$getgenre = 
				DB::table('spotify_genres as t1')
				->select('t1.*','t2.item_id')
				->leftJoin('spotify_itemgenre_fk AS t2', function($join)
							{
							$join->on('t2.genre_id', '=', 't1.id');
							})
				->where(function($query) use ($thegenrearray_s)
							{
								if (preg_match('#^(\'|").+\1$#', $thegenrearray_s) != 1)
								{
									$query->where('t1.name','LIKE','%'.$thegenrearray_s.'%');
								}
								else
								{
									$query->where('t1.name','=',trim($thegenrearray_s, '"'));

								}
							})
				->get();


						foreach ($getgenre as $getgenre_s)
						{
							$genre_ids[]=$getgenre_s->item_id;
						}
							


					}


					$genre_ids=array_unique(array_filter($genre_ids));

					if (empty($genre_ids))
					$this->returnEmpty($claim_queue_id);


                }


                $theresultset=array();
                $offset=0;
                $elementincreaser=100;
                $try=true;
                while($try)
                {
                    $getresults_arr=array();

                    $fulltextsearchstring=Helperfunctions::instance()->fullTextWildcards($searchname,1);
                    
                $getresults = 
					DB::table('spotify_items as t1')
					->select('t1.*')
					->where(function($query) use ($searchtype,$genre_ids)
							{
								if ($searchtype=='artist' && !empty($genre_ids)) {
									$query->whereIn('t1.id',$genre_ids);
								}
							})
					->where(function($query) use ($searchname,$isfulltextsearch)
						{
							if ($searchname!='' && $isfulltextsearch!='1') {
								$query->where('t1.name','LIKE', '%'.$searchname.'%');
							}
						})
                    ->where(function($query) use ($searchname,$isfulltextsearch,$fulltextsearchstring)
						{
							if ($searchname!='' && $isfulltextsearch=='1') {
								$query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)" , $fulltextsearchstring);
							}
						})
                    ->where('t1.id','>',$lastid)
					->where('t1.type','=',$searchtype)
					->where('t1.followercount', '>=', $inputfolmin)
                    ->where('t1.followercount', '<=', $inputfolmax)
                    ->where(function($query)
						{
							if ($this->artistswithoutgenres=='1') {
								$query->where(function ($query) {
									$query->where('t1.genres', '=', '')
										->orWhereNull('t1.genres');
								});
							}
						})
					->where(function($query)
						{
							if ($this->claimedshow=='1') {
								$query->orWhere('t1.claimed','=',1);
							}
							if ($this->claimedshow2=='1') {
								$query->orWhere('t1.claimed','=',3);
							}
							if ($this->notclaimedshow=='1') {
								$query->orWhere('t1.claimed','=',2);
							}
							if ($this->unknownshow=='1') {
								$query->orWhere('t1.claimed','=',0);
							}
							
                        })
                    ->orderByRaw('t1.id ASC')
                    ->offset($offset)
                    ->limit($elementincreaser)
                    ->get();


                    foreach ($getresults as $getresults_s)
						{
                            $theresultset[]=$getresults_s;
                            $getresults_arr[]=$getresults_s;
                        }
                        
                        $offset=$offset+$elementincreaser;


                        if(count(array_filter($getresults_arr))<$elementincreaser)
                        $try=false;

                    }


                    $theresultset=array_filter($theresultset);
                    $resultsetcount=count($theresultset);

                        if($resultsetcount==0)
                        {
                            echo 'No results found, probably finished';exit;
                        }

         if($item_count_old==0)
         {
            $item_count=$resultsetcount;
            $item_left=$item_count;

            DB::table('spotify_artists_claim_queue')
            ->where('id', $claim_queue_id)
            ->update([
                'item_count' => $item_count,
                'item_left' => $item_left
                ]);


         }
         else
         {
            $item_count=$item_count_old;
            $item_left=$item_left_old;

         }


         $all_i=0;
         foreach($theresultset as $theresultset_s)
			{



      SpotifyHelper::instance()->getSingleArtistClaimState($theresultset_s->itemid);




                $item_left--;

                DB::table('spotify_artists_claim_queue')
            ->where('id', $claim_queue_id)
            ->update([
                'lastid' => $theresultset_s->id,
                'item_left' => $item_left,
                ]);


                $all_i++;
                if ($all_i > 0 && $all_i % 10 == 0)
                {
                    sleep(1);

                    if(!$this->checkIfTurnedOff())
                    {
                        DB::table('spotify_artists_claim_queue')
                        ->where('id', $claim_queue_id)
                        ->update([
                            'inprogress' => '1',
                            ]);
                        echo 'Process has been turned off.';exit;
                    }

                    if(!$this->checkIfStillRunning($claim_queue_id))
                    {
                        
                        echo 'Process process state has been changed.';exit;
                    }
                }
                
                if ($all_i > 0 && $all_i % 100 == 0)
                    sleep(4);
            }


            if($item_left==0)
            {

            DB::table('spotify_artists_claim_queue')
            ->where('id', $claim_queue_id)
            ->update([
                'inprogress' => '0',
                ]);

            }

            echo 'Successfully finished';exit;

		
    }

    private function returnEmpty($claim_queue_id)
    {


        DB::table('spotify_artists_claim_queue')
            ->where('id', '=', $claim_queue_id)
            ->delete();


            exit;


    }

    private function checkIfTurnedOff()
    {

        $checkIfTurnedOn = DB::table('spotify_cron_setter')
        ->where('name', '=', 'getartistsclaimstate')
        ->first();

        if($checkIfTurnedOn->id!='' && $checkIfTurnedOn->id>0)
        {
            if($checkIfTurnedOn->state=='1')
            {
            return true;
            }
        }
        

            return false;


    }

    private function checkIfStillRunning($claim_queue_id)
    {

        $checkIfStillRunning = DB::table('spotify_artists_claim_queue')
        ->where('id', '=', $claim_queue_id)
        ->first();

        if($checkIfStillRunning->id!='' && $checkIfStillRunning->id>0)
        {
            if($checkIfStillRunning->inprogress=='2')
            {
            return true;
            }
        }
        

            return false;


    }



}
