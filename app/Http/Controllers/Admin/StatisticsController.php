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


class StatisticsController extends Controller
{
    
    private $settings;

    public function __construct()
    {

		$this->middleware('auth');


        $this->generateConfig();

	$this->settings=$this->getSettings();


	}

	
    public function getPage(Request $request)
    {

        
        
$ipAddress = $_SERVER['REMOTE_ADDR'];


        $userid = Auth::id();
        
        $theresultset=DB::table('spotify_statistics AS t1')
                    ->select('t1.*')
					->orderByRaw('t1.id ASC')
                    ->get();

                    $statistics=new \stdClass();
                    foreach ($theresultset as $theresultset_s)
                    {
                        $thename=$theresultset_s->realname;
                        $statistics->$thename=$theresultset_s->realvalue;
                    }


       $gen_acc_countries=DB::table('spotify_statistics_gen_country AS t1')
                    ->select('t1.*')
                    ->where('t1.generated', '=', '1')
					->orderByRaw('t1.account_count DESC')
                    ->get();



        
		
		$meta=array(

		'title' => 'Statistics - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Statistics - Admin Panel',

		'keywords' => '',

	);




		return view('admin/statistics', [
            'statistics'=>$statistics,
            'gen_acc_countries'=>$gen_acc_countries,
            'meta' => $meta,
		]);
		
		

	}

	
	
}
