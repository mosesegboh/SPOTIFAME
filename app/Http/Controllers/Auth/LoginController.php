<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	
	protected $username;
	
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
		
		$this->username = $this->findUsername();
    }
	
	/**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');
 
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
 
        request()->merge([$fieldType => $login]);
 
        return $fieldType;
    }
 
    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }


    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string|min:5|max:255',
            'password' => 'required|string|regex:/[0-9]/|min:6',
            'g-recaptcha-response' => config('myconfig.istestsite') ? '' :'required|captcha'
        ]);
    }
	
	
	public function showLoginForm()
    {
		
		
		$meta=array(

		'title' => 'Login - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Login - Admin Panel',

		'keywords' => '',

	);
		
        return view('login', ['meta' => $meta]);
    }
	
	public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/admin/login');
    }
	
	
}
