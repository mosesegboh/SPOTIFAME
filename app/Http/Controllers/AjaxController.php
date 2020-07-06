<?php

namespace App\Http\Controllers;

use \Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;


use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

use Illuminate\Support\Facades\Crypt;

class AjaxController extends Controller
{
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

	
	public function suggestGenre(Request $request) {

		$userid=Auth::id();

        $searchterm=$request->input('term');

        $row_get = DB::table('spotify_genres AS t1')
		->where('t1.name','LIKE',$searchterm.'%')
        ->orderByRaw('t1.item_count DESC')
        ->offset(0)
		->limit(10)
		->get();

		foreach ($row_get as $row) {
            $genres[]=$row;
            
		}

        foreach ($genres as $genres_s)
        {
            $s[] = array('label' => $genres_s->name, 'value' => $genres_s->id);
        }
     


	return response()->json($s, 200);
			


	}


}

