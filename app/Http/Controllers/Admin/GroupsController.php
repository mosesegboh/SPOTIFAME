<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;
use Carbon\Carbon;

class GroupsController extends Controller
{
    private $settings;

    private $orderby;

    private $groups=array();

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


        if ($request->input('pagenum'))
		$offset = ($request->input('pagenum')-1)*$this->settings['sp_perpage'];
		else
		$offset = '0';

        if($request->input('orderby') =='' || !$request->input('orderby'))
		$orderbyget='';
		else
        $orderbyget=$request->input('orderby');
        



        $this->orderby='t1.timestamp DESC'; //default
		
		


        $theresultset = 
					DB::table('spotify_groups as t1')
                    ->select('t1.*')
					->orderByRaw($this->orderby)
					->offset($offset)
					->limit($this->settings['sp_perpage'])
					->get();
                    
                    
                    $s_c=0;
					foreach($theresultset as $theresultset_s)
								{



									$theresultset[$s_c]=$theresultset_s;
									$s_c++;
								}

	$this->groups=$theresultset;

								
								$item_count = 
                                DB::table('spotify_groups as t1')
									->count();


            

                                    
		
		$meta=array(

		'title' => 'Our Created Groups - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Our Created Groups - Admin Panel',

		'keywords' => '',

	);

        $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}

		

		return view('admin/groups', [
		'groups'=>$this->groups,
		'meta' => $meta,
		'item_count'=> number_format($item_count),
		'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
		'pagination'=>$pagination,
        'paginationdisplay'=>$paginationdisplay,
		]);



    }
    

    public function returnEmpty(Request $request)
	{

		$meta=array(

			'title' => 'Our Created Groups - No Results - Admin Panel | '.config('myconfig.config.sitename_caps'),
	
			'description' => 'Our Created Groups - No Results - Admin Panel',
	
			'keywords' => '',
	
		);

		$paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,0,10);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}
		
		return view('admin/groups', [
		'meta' => $meta,
		'item_count'=> 0,
		'orderby'=>'','i2'=>0,'perpage'=>0,
		'pagination'=>'','paginationdisplay'=>$paginationdisplay,
		]);
		

	}
	
	
	
}
