<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

class GetSpotifySearches extends Command
{
    private $spotifyapi;
    private $market;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:getspotifysearches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Spotify Searches: Artists and Playlists';

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


        $checkIfTurnedOn = DB::table('spotify_cron_setter')
        ->where('name', '=', 'getspotifysearches')
        ->first();


        if($checkIfTurnedOn->id!='' && $checkIfTurnedOn->id>0)
        {
            if($checkIfTurnedOn->state!='1')
            {
            echo 'Process is turned off.';
            return;
            }
        }
        else
        {
            echo 'No setting found.';
            return;

        }


		$checkIfRunning = DB::table('spotify_search_cache')
        ->where('inprogress', '=', '2')
        ->first();
        
        if($checkIfRunning->id!='' && $checkIfRunning->id>0)
        {
            echo 'Another process is already running.';
            return;
        }


            $getItemToProcess = DB::table('spotify_search_cache')
            ->where('inprogress', '=', '1')
            //->orderByRaw('id ASC')
            ->orderByRaw('userid!=0 DESC,RAND()')
            ->first();

            


            if($getItemToProcess->id!='' && $getItemToProcess->id>0)
            {
                echo 'There are searches to process so starting...';
                DB::table('spotify_search_cache')
                ->where('id', '=', $getItemToProcess->id)
                ->update(['inprogress' => '2',
                ]);

                $keyword_id=$getItemToProcess->id;
            }
            else
            {
                echo 'There are no searches to process...';
                return;
            }




           $thesearchstring=base64_decode($getItemToProcess->searchstring);

           $thesearchstring_expl=explode('_',$thesearchstring);

           $searchtype=$thesearchstring_expl[0];
           $searchtype_pl=$searchtype.'s';

           $title=$thesearchstring_expl[1];
           $yearfromto=$thesearchstring_expl[2];
           $this->market=$thesearchstring_expl[3];
           $searchgenre=$thesearchstring_expl[4];
           $inputfolmin_cache=$thesearchstring_expl[5];
           $inputfolmax_cache=$thesearchstring_expl[6];


           if($searchgenre!='')
           {
               $searchgenre=' '.$searchgenre;
              
           }



           $this->spotifyapi=SpotifyHelper::instance()->getSpotifyTokens();


        $options=new \stdClass();
		$options->limit=50;
        $options->offset=0;

        if ($this->market!='')
        $options->market=$this->market;
        
        
        
           $try=true;
           while($try)
           {
               try {
                   $apisearchobject=$this->spotifyapi
                   ->search($title
                   .$yearfromto.$searchgenre, $searchtype,$options);
   
                   $try=false;
               break;
               } catch (\Exception $e) {
   
   
                       if ($e->getCode() == 429) {
   
                        $responseobject = $this->spotifyapi->getRequest()->getLastResponse();
                        $responsestatus=$responseobject['status'];
                        $retryAfter = $responseobject['headers']['Retry-After'];

                           sleep($retryAfter);
                           
                       }
                       else
                       {
                           
                           $try=false;
                       }
                       
               }
           }

		$item_count=$apisearchobject->$searchtype_pl->total;
	
        if($item_count==0)
        {
            
            
            echo 'There are no results.';
            DB::table('spotify_search_cache')
                        ->where('id', '=', $getItemToProcess->id)
                        ->update(['inprogress' => '10',
                        ]);
            return;
        }
        
        if($searchtype=='artist' || $searchtype=='playlist') //artist or playlist
				{
                    $curoffset=(int) 0;
                    $allfolloweritemsobject=array();
                
                    $try=true;
                    $pagenotfound=0;
                    while($curoffset<=$item_count && $try)
                    {

                        

                        $options=new \stdClass();
                            $options->limit=50;
                            $options->offset=$curoffset;

                            $try2=true;
                            while($try2)
						    {
                                try {
                                    $itemsfolresult=$this->spotifyapi->search($title
                                    .$yearfromto.$searchgenre, $searchtype,$options)->$searchtype_pl->items;
                                    $try2=false;

                                } catch (\Exception $e) {

                                        

                                    if ($e->getCode() == 429) {
                                        $responseobject = $this->spotifyapi->getRequest()->getLastResponse();
                                        $responsestatus=$responseobject['status'];
                                        $retryAfter = $responseobject['headers']['Retry-After'];

                                        sleep($retryAfter);

                                    }
                                    else
                                    {
                                    //echo $responsestatus.$retryAfter.' offset:'.$curoffset;
                            
                                        $pagenotfound++;
                                        $try2=false;
                                    }
                                }
                            }



                            foreach($itemsfolresult as $itemsfolresult_s)
                            {

                                

                                $itemsfolresult_s_followers=0;

                                if(!$itemsfolresult_s->followers)
                                {
                                $itemsfolresult_s->followers=new \stdClass();
                                $itemsfolresult_s->followers->total=0;
                                }
                                // this function is silenced, so to troubleshoot, need to check catch place!
                                if($searchtype=='playlist') //playlist
                                {

                                    $itemsfolresult_s_followers=$itemsfolresult_s
                                    ->followers
                                    ->total=SpotifyHelper::instance()->getSingleFollowerCount($this->spotifyapi,'playlist',$itemsfolresult_s->id);
                                    
                                }
                                // this function is silenced, so to troubleshoot, need to check catch place!

                                $itemsfolresult_s_followers=$itemsfolresult_s->followers->total;


                                //getclaimedstate
                                if($searchtype=='artist') //artist
                                {

                                }
                                //getclaimedstate

                                $allfolloweritemsobject_insertable[]=$itemsfolresult_s;
                            
                            }
                
                //print_r($allfolloweritemsobject);exit;
                        $curoffset+=50;


                        if(!$this->checkIfStillRunning($keyword_id))
                        {
                            
                            echo 'Process process state has been changed.';exit;
                        }

                        if($pagenotfound>5)
                        $try=false;
                    }
                }
                else
                {

                    echo 'We are not processing results other than artists and playlists.';
                    return;

                }


//insert
$insertable_spoti_result=$allfolloweritemsobject_insertable;
foreach ($insertable_spoti_result as $single_item)
{

 $imageurl='';
if($single_item->images[2]->url)
$imageurl=$single_item->images[2]->url;
elseif($single_item->images[0]->url)
$imageurl=$single_item->images[0]->url;

	if($searchtype=='artist') //artist
				{

                    

					DB::table('spotify_items')
					->updateOrInsert(
				['type' => $searchtype,
				 'itemid' => $single_item->id],
				[
					'name' => mb_substr($single_item->name,0, 500,'UTF-8'),
					'followercount' => $single_item->followers->total,
					'genres' => implode(', ', $single_item->genres),
					'popularity' => $single_item->popularity,
					'imageurl' => $imageurl,
					'url' => $single_item->external_urls->spotify,
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
                                
	
				
				}
	elseif($searchtype=='playlist') //playlist
				 { 
          

                    
                    DB::table('spotify_items')
					->updateOrInsert(
				['type' => $searchtype,
				 'itemid' => $single_item->id],
				[
					'name' => mb_substr($single_item->name,0, 500,'UTF-8'),
					'followercount' => $single_item->followers->total,
					'imageurl' => $imageurl,
					'url' => $single_item->external_urls->spotify,
					'ownerurl' => $single_item->owner->external_urls->spotify,
					'ownername' => mb_substr($single_item->owner->display_name,0, 500,'UTF-8'),
					'description' => $single_item->description,
					'collaborative' => $single_item->collaborative,
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

                 }


                 $updatedOrInsertedRecord2='';

				 $updatedOrInsertedRecord2 = DB::table('spotify_items')
					->where('type', '=', $searchtype)
					->where('itemid', '=', $single_item->id)
					->first();

					$item_id=$updatedOrInsertedRecord2->id;

				 DB::table('spotify_itemkeyword_fk')
					->updateOrInsert(
				['item_id' => $item_id,
				 'keyword_id' => $keyword_id],
						);




						//genres
			if($searchtype=='artist') //artist
				{
						foreach($single_item->genres as $singlegenre)
						{
			
							$singlegenre=strtolower(trim($singlegenre));
			
			
									$getGenreRecord = DB::table('spotify_genres')
								->where('name', '=', $singlegenre)
								->first();
			
								$genreid='';
								if($getGenreRecord->id >0)
								{
			
									$genreid=$getGenreRecord->id;
			
								}
								else
								{
            

			
									DB::table('spotify_genres')
								->insert([
								'name' => $singlegenre,
								'firstoccured_type' => $searchtype,
							'dt' => Carbon::now()
                            ]);


                            $last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_genres')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}
                            



									$genreid= $last_id;
			
								}
								
			
								DB::table('spotify_itemgenre_fk')
								->updateOrInsert(
							['item_id' => $item_id,
							 'genre_id' => $genreid],
									);
			
			
			
						}
					}

					//genres


}

//insert


$item_count=count(array_filter($allfolloweritemsobject_insertable));

//get real count
$item_count = 
DB::table('spotify_items as t1')
->leftJoin('spotify_itemkeyword_fk AS t2', function($join)
        {
        $join->on('t2.item_id', '=', 't1.id');
        })
    ->where('t2.keyword_id', '=', $getItemToProcess->id)
    ->count();
//get real count



        DB::table('spotify_search_cache')
                        ->where('id', '=', $getItemToProcess->id)
                        ->update([
                            'inprogress' => '0',
                            'item_count' => $item_count
                        ]);


                        


		
		
		
    }


    private function checkIfStillRunning($keyword_id)
    {

        $checkIfStillRunning = DB::table('spotify_search_cache')
        ->where('id', '=', $keyword_id)
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
