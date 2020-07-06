<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class PasswordChangeController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'oldpassword' => array('required'),
            'password' => array('required','string','min:6','regex:/[0-9]/','confirmed'),
        ];
    }
    

    public function changePassword(Request $request)
    {

        $this->validate($request, $this->rules());


            $hashedPassword = Auth::user()->password;

            if (\Hash::check($request->oldpassword , $hashedPassword )) {
 
                if (!\Hash::check($request->password , $hashedPassword)) {
        
                     $users =User::find(Auth::user()->id);
                     $users->password = bcrypt($request->password);
                     User::where( 'id' , Auth::user()->id)->update( array( 'password' =>  $users->password));
        
                  
                     return redirect()->back()->with('success', ['Password updated successfully.']);   
              
                   }
        
                   else{
                       
                         
                         return redirect()->back()->with('error', ['The new password can not be the old password.']);
                       }
        
            }
        
            else{

                      return redirect()->back()->with('error', ['The old password is not correct.']);   
            }
        
              
        


    }



	public function getPage(Request $request)
    {

      
		
		$meta=array(

		'title' => 'Change Password | '.config('myconfig.config.sitename_caps'),

		'description' => 'Change Password',

		'keywords' => '',

	);
		
		return view('admin.passwordchange', ['meta' => $meta]);
    }
	
}
