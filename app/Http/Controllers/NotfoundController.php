<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class NotfoundController extends Controller
{
	
	public function getPage()
    {
		
		$meta=array(

		'title' => 'Sorry, the page is not found, it does not exist! | '.config('myconfig.config.sitename_caps'),

		'description' => 'The page does not exist.',

		'keywords' => 'page not found',

	);
		
        return view('notfound', ['meta' => $meta,'choice_notfound'=>'active']);
		
	}
	
	
}
