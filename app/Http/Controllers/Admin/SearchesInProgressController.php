<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use Carbon\Carbon;

use GuzzleHttp\Client;


class SearchesInProgressController extends Controller
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
					DB::table('spotify_search_cache as t1')
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
                                    $searchtype='';
                                    $searchtype=$thesearchstring_expl[0];
                                    $title='';
                                    $title=$thesearchstring_expl[1];
                                    $yearfromto='';
                                    $yearfromto=$thesearchstring_expl[2];


                                    $market='';
                                    $market=$thesearchstring_expl[3];
                                    $searchgenre='';
                                    $searchgenre=$thesearchstring_expl[4];
                                    $inputfolmin_cache='';
                                    $inputfolmin_cache=$thesearchstring_expl[5];
                                    $inputfolmax_cache='';
                                    $inputfolmax_cache=$thesearchstring_expl[6];
                                   
                                  
    $processedresults[$s_c]->searchtype=$searchtype;

    $processedresults[$s_c]->url='?pagenum=1&title='.$title.'&searchset=1&advopen=1&searchtype='.$searchtype;
                            
    $processedresults[$s_c]->searchstring='Query:'.$title;

    if($searchgenre!='')
    {

        $searchgenreurl=str_replace('"','',$searchgenre);

        $searchgenreurl=join(",", array_filter(explode('genre:',$searchgenreurl)));
        

    $processedresults[$s_c]->url.='&genres='.$searchgenreurl;

    $processedresults[$s_c]->searchstring.=', Genres:'.$searchgenreurl;
    }

    if($market!='')
    {
    $processedresults[$s_c]->url.='&marketselect='.$market;
    $processedresults[$s_c]->searchstring.=', Market:'.$market;
    }

    if($yearfromto!='')
                    {
       $processedresults[$s_c]->url.='&yearfrom='.trim(str_replace('year:','',explode('-',$yearfromto)[0]));
       $processedresults[$s_c]->url.='&yearto='.trim(explode('-',$yearfromto)[1]);

       $processedresults[$s_c]->searchstring.=', Years:'.str_replace('year:','',$yearfromto);
                    }

    if($inputfolmin_cache!='' && $inputfolmax_cache!='')
                    {
       $processedresults[$s_c]->url.='&followers='.$inputfolmin_cache.';'.$inputfolmax_cache;

       //$processedresults[$s_c]->searchstring.=',Followers:'.$inputfolmin_cache.'-'.$inputfolmax_cache;
                    }
                    else
                     {
                       
  $processedresults[$s_c]->url.='&followers='.$this->rangeslidervalues[0].';'.$this->rangeslidervalues[1];

                     }

                   
                    

									$s_c++;
								}

                    $item_count = 
                    DB::table('spotify_search_cache as t1')
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
        ->where('name', '=', 'getspotifysearches')
        ->first();

    $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
    foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
      {
          $$paginationarray_key=$paginationarray_value;
    }
		
		$meta=array(

		'title' => 'Searches In Progress - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Searches In Progress - Admin Panel',

		'keywords' => '',

	);


        return view('admin/searchesinprogress', ['meta' => $meta,
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
