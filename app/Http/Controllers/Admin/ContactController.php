<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;


class ContactController extends Controller
{
    private $settings;

    private $orderby;

    private $users=array();

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
        
        $this->orderby='t1.dt DESC'; //default
        
        $namesearch=urldecode($request->input('name'));
        $emailsearch=urldecode($request->input('email'));

     
        $typesearch=$request->input('type');
      


        $theresultset = 
					DB::table('spotify_contact as t1')
                    ->select('t1.*','t2.username')
                    ->leftJoin('users AS t2', function($join)
							{
							$join->on('t2.id', '=', 't1.userid');
                            })
                    ->where(function($query) use ($typesearch){
							if ($typesearch!='') {
                               if(in_array($typesearch,array('homecontact','contact')))
                                    $query->where('t1.type','=', $typesearch);
                                    
                           }

                        })
                    ->where(function($query) use ($namesearch)
						{
							if ($namesearch!='') {
								$query->where('t1.name','LIKE', '%'.$namesearch.'%');
							}
                        })
                    ->where(function($query) use ($emailsearch)
						{
							if ($emailsearch!='') {
								$query->where('t1.email','LIKE', '%'.$emailsearch.'%');
							}
                    })
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

	$this->letters=$theresultset;

								
								$item_count = 
                                DB::table('spotify_contact as t1')
                                ->where(function($query) use ($typesearch){
                                    if ($typesearch!='') {
                                       if(in_array($typesearch,array('homecontact','contact')))
                                            $query->where('t1.type','=', $typesearch);
                                            
                                        }
                
                                    })
                                    ->where(function($query) use ($namesearch)
                                            {
                                                if ($namesearch!='') {
                                                    $query->where('t1.name','LIKE', '%'.$namesearch.'%');
                                                }
                                            })
                                    ->where(function($query) use ($emailsearch)
                                            {
                                                if ($emailsearch!='') {
                                                    $query->where('t1.email','LIKE', '%'.$emailsearch.'%');
                                                }
                                    })
									->count();


            

                                    
		
		$meta=array(

		'title' => 'Contact - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Contact - Admin Panel',

		'keywords' => '',

	);

        $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}

		

		return view('admin/contact', [
		'letters'=>$this->letters,
		'meta' => $meta,
		'item_count'=> number_format($item_count),
		'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
		'pagination'=>$pagination,
        'paginationdisplay'=>$paginationdisplay,
		]);



	}
	
	
	
}
