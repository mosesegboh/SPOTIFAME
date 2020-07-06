<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;


class ConnectSpotifyController extends Controller
{
    
    private $settings;

    public function __construct()
    {


        $this->generateConfig();

	$this->settings=$this->getSettings();


	}


    public function grantSpotifyAccess(Request $request)
    {
        
        /*
        $hash=$request->input('hash');

        if($request->input('code')!='')
        {

        $this->spotifyapi=SpotifyHelper::instance()->grantAccessForArtistpicks(trim($request->input('code')),'');
        }
        else
        {

            

            $row_get = DB::table('spotify_accounts_auth')
            ->where('generatedstr', '=', $hash)
            ->limit(1)
            ->get();
        
            
            foreach ($row_get as $row) {
                $result_check[] = $row;
            }
        
            if(empty($result_check))
            {
                return redirect()->back()->with('error', ['The link hash is not correct!']);
                exit;
            }

        $this->spotifyapi=SpotifyHelper::instance()->grantAccessForArtistpicks('',$hash);

        }
        */


        if($request->input('code')!='')
        {

        $response = $this->spotifyapi=SpotifyHelper::instance()->grantAccessFromOutside(trim($request->input('code')));
   
        }
        else
        {
            echo 'problem';
        }

        echo $response;
        

    }
	
    public function getPage(Request $request)
    {

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        
        $hash=$request->input('hash');
        

    $row_get = DB::table('spotify_accounts_auth')
	->where('generatedstr', '=', $hash)
	->limit(1)
	->get();

	
	foreach ($row_get as $row) {
		$result_check[] = $row;
	}

	if(empty($result_check))
	{
        $okshow='0';
    }
    else
    {
        $okshow='1';
    }

        




		
		$meta=array(

		'title' => 'Connect Spotify Accounts | '.config('myconfig.config.sitename_caps'),

		'description' => 'Connect Spotify Accounts',

		'keywords' => '',

	);




		return view('connectspotify', [
            'meta' => $meta,
            'okshow' => $okshow,
            'hash'=>$hash
		]);
		
		

	}

	
	
}
