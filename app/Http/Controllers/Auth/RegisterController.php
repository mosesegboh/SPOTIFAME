<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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
            //'name' => array('required','string','min:5','max:255'),
			//'username' => array('required','string','min:5','max:255','unique:users'),
            'email' => array('required','string','email','max:255','unique:users'),
            'password' => array('required','string','min:6','regex:/[0-9]/','confirmed'),
            'g-recaptcha-response' => config('myconfig.istestsite') ? '' :array('required','captcha'),
            //'spotifylink' => trim($data['spotifylink'])=='' ? '' :array('regex:/^((http|https):\/\/)(?:www\.)?(?:open\.spotify\.com)(?:\/\w+(?:-\w+)*)+$/'),
            //'soundcloudlink' => trim($data['soundcloudlink'])=='' ? '' :array('regex:/^((http|https):\/\/)(?:www\.)?(?:soundcloud\.com|snd\.sc)(?:\/\w+(?:-\w+)*)+$/')
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $isartist='0';
        /*if($data['isartist']=='on')
        $isartist='1';*/

        $islabel='0';
        /*if($data['islabel']=='on')  //promoter
        $islabel='1';*/

        $ismanager='0';
        /*if($data['ismanager']=='on')  //artistmanager
        $ismanager='1';*/

        $isplaylistowner='0';
        /*if($data['isplaylistowner']=='on') //playlister
        $isplaylistowner='1';*/

        $isjournalist='0';
        /*if($data['isjournalist']=='on') //journalist
        $isjournalist='1';*/

        $isdjremixer='0';
        /*if($data['isdjremixer']=='on') //dj/remixer
        $isdjremixer='1';*/


        return User::create([
            //'name' => $data['name'],
            //'username' => $data['username'],
            'username' => $data['email'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            //'artistname' => $data['artistname'],
            //'spotifylink' => $data['spotifylink'],
            //'soundcloudlink' => $data['soundcloudlink'],
            'isartist' => $isartist,
            'islabel' => $islabel,
            'ismanager' => $ismanager,
            'isplaylistowner' => $isplaylistowner,
            'isjournalist' => $isjournalist,
            'isdjremixer' => $isdjremixer,
			'type' => User::DEFAULT_TYPE,
        ]);
    }


    public function showRegistrationForm()
    {
		
		$meta=array(

		'title' => 'Register Now | '.config('myconfig.config.sitename_caps'),

		'description' => 'Register Now',

		'keywords' => '',

	);
		
		return view('register', ['meta' => $meta]);
    }
}
