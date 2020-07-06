<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;


class SettingsController extends Controller
{
    
	private $adminsettings;
	
	private $tablearray=array('settings');

    public function __construct()
    {

		$this->middleware('auth');

	

	}

	public function updateSettings(Request $request)
    {

		$userid = Auth::id();

			$userl_expl=explode('|',auth()->user()->type);
			

			foreach ($userl_expl as $userl_s)
			{
				$userlevels[]=config('myconfig.userlevels')[$userl_s];
			}
			$userlevel= max($userlevels); 


		$row_config_get = DB::table('spotify_settings')
		->where('active', '=', '1')
		->where('visible', '=', '1')
		->where('minuserlevel', '>=', $userlevel)
		->orderByRaw('id ASC')
        ->get();

		
			foreach ($row_config_get as $row_config_get_s)
			{
				
				if($request->input('settings|'.$row_config_get_s->realname)!='')
					{
						//echo $request->input('settings|'.$row_config_get_s->realname);exit;
						
						$thevalue=$request->input('settings|'.$row_config_get_s->realname);
					
						

						if($row_config_get_s->type=='select')
						{


							$senditem='';
							$foundactive='0';
							$senditem_array=array();
							$selectvalues=array();

							$selectvalues=explode('||',$row_config_get_s->realvalue);
							
							foreach ($selectvalues as $selectvalues_s)
							{
								$isactive='0';
								
								$selectvalueactive=explode(';',$selectvalues_s);
								if($selectvalueactive['0']==$thevalue)
								{
									$isactive='1';
									$foundactive='1';
								}
								
								if($selectvalues_s!='')
								{
									
									if($isactive=='1')
								$senditem_array[]=$selectvalueactive['0'].';1';
											else
								$senditem_array[]=$selectvalueactive['0'];
											
										}
										
							}
								
							$thevalue=join('||', array_filter($senditem_array));


								if($foundactive!='1')
								{
									return redirect(config('myconfig.config.server_url').'admin/settings')->with('failed','Something went wrong.');
								}



						}
						else
						{
							
							if($row_config_get_s->type=='checkbox')
							{
								$thevalue=1;
							}

						}





						
					}
					else
					{

						if($row_config_get_s->type=='checkbox')
							{
								$thevalue=0;
							}

					}

					DB::table('spotify_settings')
							->where('realname', '=', $row_config_get_s->realname)
							->where('active', '=', '1')
							->where('visible', '=', '1')
							->where('minuserlevel', '>=', $userlevel)
							->update([
								'realvalue' => $thevalue,
							]);





			}



			$row_config_get = DB::table('spotify_cron_setter')
		->where('active', '=', '1')
		->where('visible', '=', '1')
		->where('minuserlevel', '>=', $userlevel)
		->orderByRaw('id ASC')
		->get();
		
		foreach ($row_config_get as $row_config_get_s)
			{

				if($request->input('cron|'.$row_config_get_s->name)!='')
					{
						$thevalue=1;

					}
					else
					{
						$thevalue=0;
					}


					DB::table('spotify_cron_setter')
							->where('name', '=', $row_config_get_s->name)
							->where('active', '=', '1')
							->where('visible', '=', '1')
							->where('minuserlevel', '>=', $userlevel)
							->update([
								'state' => $thevalue,
							]);

			}


			
		return redirect(config('myconfig.config.server_url').'admin/settings')->with('success','Settings saved successfully.');

	}

    public function getPage(Request $request)
    {


$ipAddress = $_SERVER['REMOTE_ADDR'];


		$userid = Auth::id();

		$userl_expl=explode('|',auth()->user()->type);
			

			foreach ($userl_expl as $userl_s)
			{
				$userlevels[]=config('myconfig.userlevels')[$userl_s];
			}
			$userlevel= max($userlevels); 



		$row_config_get = DB::table('spotify_settings')
		->where('active', '=', '1')
		->where('visible', '=', '1')
		->where('minuserlevel', '>=', $userlevel)
		->orderByRaw('id ASC')
        ->get();

			foreach ($row_config_get as $row)
			{

						
						if($row->type=='select')
						{
							$selectvalues=explode('||',$row->realvalue);
							$row->realvalue='';
							foreach ($selectvalues as $selectvalues_s)
							{
								$isactive='0';
								
								$selectvalueactive=explode(';',$selectvalues_s);
								if($selectvalueactive['1']=='1')
								{
									$isactive='1';
								}
								
								if($selectvalues_s!='')
								{
						
						$row->realvalue.='<option value="'.$selectvalueactive['0'].'"';
									
									if($isactive=='1')
						$row->realvalue.=' selected="selected"';
									
						$row->realvalue.='>'.$selectvalueactive['0'].'</option>';
								}
								
							}
							
						}
							
							
						$row->tablename='settings';
						$this->adminsettings[] = $row;
			}



			$row_config_get = DB::table('spotify_cron_setter')
		->where('active', '=', '1')
		->where('visible', '=', '1')
		->where('minuserlevel', '>=', $userlevel)
		->orderByRaw('id ASC')
		->get();
		
		foreach ($row_config_get as $row)
			{
				$this->cronsettings[] = $row;
			}

		
		$meta=array(

		'title' => 'Settings - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Settings - Admin Panel',

		'keywords' => '',

	);


		return view('admin/settings', ['meta' => $meta,
		'adminsettings'=> $this->adminsettings,
		'cronsettings'=>$this->cronsettings,
		]);
		



	}
	
	
	
}
