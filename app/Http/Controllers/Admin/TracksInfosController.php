<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;
use Carbon\Carbon;

class TracksInfosController extends Controller
{
    private $settings;

    private $orderby;

    private $tracks=array();

    public function __construct()
    {

		$this->middleware('auth');


        $this->generateConfig();

	$this->settings=$this->getSettings();

	

	}

	public function getInfoAboutTrack(Request $request)
    {

	}


    public function getPage(Request $request)
    {


		$spotifyapi=SpotifyHelper::instance()->getSpotifySearchTokens();


		if($request->input('track'))
		{

			$trackcontent='';

			$track=$request->input('track');

			$trackresponse=array();
			$trackresponse=SpotifyHelper::instance()->getSpotifyTrackItemId($track);

			if($trackresponse['id']!='')
			$trackid=$trackresponse['id'];
			else
			$trackid=$track;
	
			
			
			$found=0;

			$row_get = DB::table('spotify_tracks_infos AS t1')
                        ->select('t1.*')
                        ->where('t1.itemid','=',$trackid)
                        ->limit(1)
						->get();

						$results_get=array();
                        
						foreach ($row_get as $row_get_s) {

							$results_get[]=$row_get_s;

						}
						if(!empty($results_get))
						{
							$found=1;	
						}


			if($found)
			{
				
				$firsttrackdata=unserialize($results_get[0]->basicinfo);
				$secondtrackdata=unserialize($results_get[0]->audiofeatures);
				$thirdtrackdata=unserialize($results_get[0]->audioanalysis);
				 

			}
			else
			{

			
				if($trackid!='')
				{

//1.)
$secondtrackdata=SpotifyHelper::instance()->getSpotifyItem($spotifyapi,$trackid,'track');



//2.)
$secondtrackdata=SpotifyHelper::instance()->getAudioFeatures($spotifyapi,$trackid);


//3.)
$thirdtrackdata=SpotifyHelper::instance()->getAudioAnalysis($spotifyapi,$trackid);


						}


			}

			

		}


		if(!empty($firsttrackdata))
			$trackcontent.='<br><p>Basic Information:</p><div style="max-height:300px;overflow-y:scroll;"><pre>'.print_r($firsttrackdata, true).'</pre></div></br>';

		if(!empty($secondtrackdata))
			$trackcontent.='<br><p>Audio Features:</p><div style="max-height:500px;overflow-y:scroll;"><pre>'.print_r($secondtrackdata, true).'</pre></div></br>';

		/*if(!empty($thirdtrackdata))
			$trackcontent.='<br><p>Audio Analysis:</p><div style="max-height:500px;overflow-y:scroll;"><pre>'.print_r($thirdtrackdata, true).'</pre></div></br>';
			*/

	if(!empty($thirdtrackdata))
	{
		$thirdtrackcontent=print_r($thirdtrackdata, true);
	}

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
		
		

		if(!$found && $firsttrackdata->name!='')
			{

		DB::table('spotify_tracks_infos')
		->updateOrInsert(
	['itemid' => $firsttrackdata->id],
	[
		'name' => mb_substr($firsttrackdata->name,0, 500,'UTF-8'),
		'artisturl' => $firsttrackdata->artists[0]->external_urls->spotify,
		'artistname' => mb_substr($firsttrackdata->artists[0]->name,0, 500,'UTF-8'),
		'albumurl' => $firsttrackdata->album->href,
		'albumname' => mb_substr($firsttrackdata->album->name,0, 500,'UTF-8'),
		'basicinfo' => serialize($firsttrackdata),
		'audiofeatures' => serialize($secondtrackdata),
		'audioanalysis' => serialize($thirdtrackdata),
	'dt' => Carbon::now()]
			);
						

						$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_tracks_infos')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}

			}

	


        $theresultset = 
					DB::table('spotify_tracks_infos as t1')
                    ->select('t1.*')
					->orderByRaw($this->orderby)
					->offset($offset)
					->limit($this->settings['sp_perpage'])
					->get();
                    
                    
                    $s_c=0;
					foreach($theresultset as $theresultset_s)
								{


									$s_c++;
								}

	$this->tracks=$theresultset;

								
								$item_count = 
                                DB::table('spotify_tracks_infos as t1')
									->count();


            

                                    
		
		$meta=array(

		'title' => 'Tracks Infos - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Tracks Infos - Admin Panel',

		'keywords' => '',

	);

        $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}

		

		return view('admin/tracksinfos', [
		'thirdtrackcontent'=>json_encode($thirdtrackcontent),
		'trackcontent'=>$trackcontent,
		'tracks'=>$this->tracks,
		'meta' => $meta,
		'item_count'=> number_format($item_count),
		'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
		'pagination'=>$pagination,
        'paginationdisplay'=>$paginationdisplay,
		]);



	}
	
	
	
}
