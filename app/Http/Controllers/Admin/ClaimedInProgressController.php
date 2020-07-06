<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

use GuzzleHttp\Client;


class ClaimedInProgressController extends Controller
{
    
    private $settings;
    private $rangeslidervalues=array();

    public function __construct()
    {

		$this->middleware('auth');

	$this->generateConfig();

	$this->settings=$this->getSettings();


    $this->rangeslidervalues=array(
		$this->settings['sp_range_min'],
		$this->settings['sp_range_max'],
		$this->settings['sp_range_break']

	);

	}

	
    public function getPage(Request $request)
    {


$ipAddress = $_SERVER['REMOTE_ADDR'];

		$userid = Auth::id();
        
        $perpage=50;
        if ($request->input('pagenum'))
		$offset = ($request->input('pagenum')-1)*$perpage;
		else
		$offset = '0';

        if($request->input('getcurrentid'))
        $getcurrentid=$request->input('getcurrentid');

        $donotshowanyofthem='0';
        if($request->input('processedshow')=='on' || !$request->input('searchset'))
        $processedshow='on';

        if($request->input('waitingshow')=='on' || !$request->input('searchset'))
        $waitingshow='on';
        
        if($request->input('processingshow')=='on' || !$request->input('searchset'))
        $processingshow='on';

        if($request->input('problematicshow')=='on' || !$request->input('searchset'))
        $problematicshow='on';
        
        if($processedshow!='on' && $waitingshow!='on' && $processingshow!='on' && $problematicshow!='on')
        {
            $donotshowanyofthem='1';
        }

        $processedresults = 
					DB::table('spotify_artists_claim_queue as t1')
                    ->select('t1.*')
                    ->where(function($query) use($processedshow,$waitingshow,$processingshow,$problematicshow,$donotshowanyofthem)
								{
									if ($processedshow=='on') {
                                        $query->orWhere('t1.inprogress','=',0);
                                    }
									if ($waitingshow=='on') {
                                        $query->orWhere('t1.inprogress','=',1);
                                    }
									if ($processingshow=='on') {
                                        $query->orWhere('t1.inprogress','=',2);
                                    }
									if ($problematicshow=='on') {
                                        $query->orWhere('t1.inprogress','=',10);
                                    }
                                    if ($donotshowanyofthem=='1') {
                                        $query->orWhere('t1.inprogress','=',-1);
                                    }
                                })
                    ->where(function($query) use($getcurrentid)
                                {
                                   if ($getcurrentid!='') {
                                       $query->where('id','=',$getcurrentid);
                                }
                    })
					->orderByRaw('t1.inprogress=2 DESC,t1.inprogress=1 DESC,t1.id DESC')
					->offset($offset)
					->limit($perpage)
                    ->get();
                    
                   

                    $s_c=0;
					foreach($processedresults as $processedresults_s)
								{

                                    $thesearchstring=base64_decode($processedresults[$s_c]->searchstring);

                                    $thesearchstring_expl=explode('_',$thesearchstring);

                                    $searchtype=$thesearchstring_expl[0];
                                    $searchname=$thesearchstring_expl[1];
                                    
                                    $searchgenrestring=$thesearchstring_expl[4];

                                    if($processedresults[$s_c]->artistswithoutgenres=='1')
                                    {
                                    $searchgenrestring='';
                                    }

            $inputfolmin=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[2],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
			$inputfolmax=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[3],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
                                    
                                  
    $processedresults[$s_c]->searchtype=$searchtype;

    $processedresults[$s_c]->url='?pagenum=1&searchset=1&searchtype='.$searchtype;
    $processedresults[$s_c]->searchstring='';

    if($searchname!='')
    {
    $processedresults[$s_c]->url.='&title='.$searchname;
    $processedresults[$s_c]->searchstring.='Query:'.$searchname.', ';
    }
                    
    if($searchgenrestring!='')
    {
    $processedresults[$s_c]->url.='&genres='.$searchgenrestring;
    $processedresults[$s_c]->searchstring.='Genres:'.str_replace(',',', ',$searchgenrestring).', ';
    }

    $processedresults[$s_c]->url.='&followers='.$thesearchstring_expl[2].';'.$thesearchstring_expl[3];
    $processedresults[$s_c]->searchstring.='Followers:'.$inputfolmin.'-'.$inputfolmax;

    

    if($processedresults[$s_c]->claimedrefresh)
    {
        $processedresults[$s_c]->url.='&claimedshow=on';
        $processedresults[$s_c]->searchstring.='<br>Claimed: yes';
    }

    if($processedresults[$s_c]->claimed2refresh)
    {
        $processedresults[$s_c]->url.='&claimedshow2=on';
        $processedresults[$s_c]->searchstring.='<br>Claimed (changed): yes';
    }

    if($processedresults[$s_c]->notclaimedrefresh)
    {
        $processedresults[$s_c]->url.='&notclaimedshow=on';
        $processedresults[$s_c]->searchstring.='<br>Not claimed: yes';

    }

    if($processedresults[$s_c]->unknownrefresh)
    {
        $processedresults[$s_c]->url.='&unknownshow=on';
        $processedresults[$s_c]->searchstring.='<br>Unknown: yes';

    }

    if($processedresults[$s_c]->artistswithoutgenres)
    {
        $processedresults[$s_c]->url.='&artistswithoutgenres=on';
        $processedresults[$s_c]->searchstring.='<br>Artist without genres: yes';

    }


									$s_c++;
								}

                    $item_count = 
                    DB::table('spotify_artists_claim_queue as t1')
                    ->select('t1.*')
                    ->where(function($query) use($processedshow,$waitingshow,$processingshow,$problematicshow,$donotshowanyofthem)
								{
									if ($processedshow=='on') {
                                        $query->orWhere('t1.inprogress','=',0);
                                    }
									if ($waitingshow=='on') {
                                        $query->orWhere('t1.inprogress','=',1);
                                    }
									if ($processingshow=='on') {
                                        $query->orWhere('t1.inprogress','=',2);
                                    }
									if ($problematicshow=='on') {
                                        $query->orWhere('t1.inprogress','=',10);
                                    }
                                    if ($donotshowanyofthem=='1') {
                                        $query->orWhere('t1.inprogress','=',-1);
                                    }
                                })
                     ->where(function($query) use($getcurrentid)
                     {
                        if ($getcurrentid!='') {
                            $query->where('id','=',$getcurrentid);
                        }
                     })
                     ->count();


        $checkIfTurnedOn = DB::table('spotify_cron_setter')
        ->where('name', '=', 'getartistsclaimstate')
        ->first();

        


    $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
    foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
      {
          $$paginationarray_key=$paginationarray_value;
    }
		
		$meta=array(

		'title' => 'Claimed Artists In Progress - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Claimed Artists In Progress - Admin Panel',

		'keywords' => '',

	);


        return view('admin/claimedinprogress', ['meta' => $meta,
        'processedresults'=>$processedresults,
        'item_count'=> number_format($item_count),
        'i2'=>$i2,
        'pagination'=>$pagination,
        'paginationdisplay'=>$paginationdisplay,
        'processedshow'=>$processedshow,
        'waitingshow'=>$waitingshow,
        'processingshow'=>$processingshow,
        'problematicshow'=>$problematicshow,
        'checkIfTurnedOn'=>$checkIfTurnedOn,
        ]);
        
        
        
	}
	
	
	
}
