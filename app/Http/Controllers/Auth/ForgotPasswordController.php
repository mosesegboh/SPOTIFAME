<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException; 

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest');
    }


    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        
        $request->validate([
			'email' => 'required|email',
			'g-recaptcha-response' => config('myconfig.istestsite') ? '' :'required|captcha'
							]);
    
    }
	
	public function showLinkRequestForm()
    {
		
		
		$meta=array(

		'title' => 'Forgot Password | '.config('myconfig.config.sitename_caps'),

		'description' => 'Forgot Password',

		'keywords' => '',

	);
		
		return view('admin.passwords.email', ['meta' => $meta]);
    }
	
	
	
}
