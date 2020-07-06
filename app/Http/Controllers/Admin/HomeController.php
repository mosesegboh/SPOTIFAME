<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;


use App\User;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

use Mail;

class HomeController extends Controller
{
	
	
	
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    public function getPage(Request $request)
    {
		
		$userid = Auth::id();
		
		$channels=array();
		$genres=array();
		$items=array();
		
		$serializedarray=Helperfunctions::instance()->SerializeAdminRequestSearch($request);
		
		
		foreach ($serializedarray as $serializedarray_key => $serializedarray_value)
		{
			$$serializedarray_key=$serializedarray_value;
		}
		
		//$fromdate $currentdate $todate $perpage $i2 $monetize $orderby $ch $genre $quotation_mark $query_words1 $query_words $titlesearch2 $youtubeid $titledecoded
		
		//$realorderby $realmonetize $realgenre $realch $realfromtodate $anysearch
		
		//last logcheck
	
		
		
		
		$meta=array(

		'title' => 'Home - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Home - Admin Panel',

		'keywords' => '',

	);
		

		return view('admin/home', ['meta' => $meta]);
		
	}
	

	
   	/**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => array('required','string','min:1','max:255'),
            'email' => array('required','string','email','max:255','unique:users,email,'.Auth::id()),
            'description' => array('required','min:5','max:2048'),
        ]);
    }

  	function sendHomeForm(Request $request) {

    $userid=Auth::id();

    $userobject=User::findOrFail($userid);

    $data=$request->all();

        $validator = $this->validator($data);
        $validator->validate();



            DB::table('spotify_contact')->insert(
              [
                'email' => $data['email'],
                'name' => $data['name'],
                'subject' => 'HomePage Form Letter',
                'description' => $data['description'],
                'userid' => $userid,
                'phone' =>  $data['phone'],
                'djname' => $data['djname'],
                'dt' => Carbon::now(),
              'timestamp' => Carbon::now()->timestamp
              ]
            );


            Mail::send('emails.sitecontact',
            array(
                'sender_name' => $data['name'],
                'sender_email' => $data['email'],
                'sender_phone' => $data['phone'],
                'subform' => 'HomePage Form Letter',
                'bodymessage' => $data['description']
            ), function($message) use ($data)
        {
            $message->from($data['email']);
            $message->to(config('myconfig.contact.sendto'), 'Admin')->subject('HomePage Form Letter');
        });


return redirect()->back()->with('success', ['Form sent successfully.']);   



  	}
		
	
	
	
}
