<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;


class ArtistPageController extends Controller
{
    

    public function __construct()
    {

		$this->middleware('auth');

	

	}

    public function getPage(Request $request, $artistid = null)
    {


$ipAddress = $_SERVER['REMOTE_ADDR'];
$userid = Auth::id();

if($artistid=='')
return redirect('notfound');


$artist_get = DB::table('spotify_items as t1')
					->select('t1.*')
					->where('t1.id', '=', $artistid)
					->where('t1.type', '=', 'artist')
					->limit(1)
					->get();


					foreach ($artist_get as $row) {
						$theartist[] = $row;
					}

					if(is_null($theartist) || 
					empty($theartist))
					{
						return redirect('notfound');
					}

					$theartist=$theartist[0];
					$genres=array();
					if($theartist->genres!='')
					{
						$genres_expl=explode(',',$theartist->genres);
						sort($genres_expl);

						foreach ($genres_expl as $genres_expl_s)
						{
						$genres[]=trim($genres_expl_s);
						}

						$genres=array_filter($genres);
					}



		$meta=array(

		'title' => 'Artist: '.$theartist->name.' | '.config('myconfig.config.sitename_caps'),

		'description' => 'Artist: '.$theartist->name,

		'keywords' => '',

	);


		return view('admin/artistpage', [
			'meta' => $meta,
			'theartist' => $theartist,
			'genres'=>$genres,
		]);
		



	}

	
	
	
}
