<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResetPassController extends Controller
{
	
    public function getPage(Request $request)
    {
		
		$code=$request->input('code');
	$hmac_salt=$code;
	$email =$_REQUEST["email"];
		
		
		
		$meta=array(

		'title' => 'Reset Password | '.config('myconfig.config.sitename_caps'),

		'description' => 'Reset Password',

		'keywords' => '',

	);
		
        return view('resetpass', ['meta' => $meta]);
    }
	
}
