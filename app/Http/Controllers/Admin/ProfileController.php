<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use App\Helpers\SpotifyHelper;
use Carbon\Carbon;

class ProfileController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');
    }

    
    /**
     * Get the basic validation rules.
     *
     * @return array
     */
    protected function basicrules(array $data,object $userobject)
    {
        return [
            'username' => array('required','string','min:5','max:255','unique:users,username,'.Auth::id()),
            'email' => array('required','string','email','max:255','unique:users,email,'.Auth::id()),
            'yourwebsite' => trim($data['yourwebsite'])=='' ? '' :array('regex:/^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+((\.[a-zA-Z]{2,3})+)(\/(.)*)?(\?(.)*)?/i'),
            'soundcloudlink' => trim($data['soundcloudlink'])=='' ? '' :array('regex:/^((http|https):\/\/)(?:www\.)?(?:soundcloud\.com|snd\.sc)(?:\/\w+(?:-\w+)*)+$/'),
            'facebooklink' => trim($data['facebooklink'])=='' ? '' :array('regex:/^((http|https):\/\/)(?:www\.)?(mbasic.facebook|m\.facebook|facebook|fb)\.(com|me)\/(?:(?:\w\.)*#!\/)?(?:pages\/)?(?:[\w\-\.]*\/)*([\w\-\.]*).+/i'),
            'instagramlink' => trim($data['instagramlink'])=='' ? '' :array('regex:/^((http|https):\/\/)(?:www\.)?(?:instagram.com|instagr.am)\/.+/i'),
            'twitterlink' => trim($data['twitterlink'])=='' ? '' :array('regex:/^((http|https):\/\/)(?:www\.)?twitter\.com\/(#!\/)?[a-zA-Z0-9_]+$/i'),
            'tiktoklink' => trim($data['tiktoklink'])=='' ? '' :array('regex:/^((http|https):\/\/)(www[.])?tiktok.com\/.+/i'),
            'otherlink' => trim($data['otherlink'])=='' ? '' :array('regex:/^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+((\.[a-zA-Z]{2,3})+)(\/(.)*)?(\?(.)*)?/i'),
            'djartistlink' => (trim($data['djartistlink'])!='' && $userobject->isdjremixer)  ? array('regex:/^(https?:\/\/(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/artist\/)([a-zA-Z0-9]+)(.*)$/') : '',
            'remixgenre' => (trim($data['djartistlink'])!='' && $userobject->isdjremixer) ? array('required') : '',
            'articlepublish' => $userobject->isjournalist ? array('required','in:1,0') :'',
            'articlelink1' =>  $userobject->isjournalist ? array('different:articlelink2','different:articlelink3','required','regex:/^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+((\.[a-zA-Z]{2,3})+)(\/(.)*)?(\?(.)*)?/i') :'',
            'articlelink2' =>  $userobject->isjournalist ? array('different:articlelink1','different:articlelink3','required','regex:/^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+((\.[a-zA-Z]{2,3})+)(\/(.)*)?(\?(.)*)?/i') :'',
            'articlelink3' =>  $userobject->isjournalist ? array('different:articlelink1','different:articlelink2','required','regex:/^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+((\.[a-zA-Z]{2,3})+)(\/(.)*)?(\?(.)*)?/i') :'',
        ];
    }

    protected function basicmessages()
    {
        return [
            'yourwebsite.regex' => 'Please add a valid website link.',
            'soundcloudlink.regex' => 'Please add a valid soundcloud link.',
            'facebooklink.regex' => 'Please add a valid facebook link.',
            'instagramlink.regex' => 'Please add a valid instagram link.',
            'twitterlink.regex' => 'Please add a valid twitter link.',
            'tiktoklink.regex' => 'Please add a valid tiktok link.',
            'otherlink.regex' => 'Please add a valid link.',
            'djartistlink.regex' => 'Please add a valid spotify artist link.',
            'remixgenre.required' => 'This field is required if you added an artist link.',
            'articlepublish.required' => 'Please decide if you would like articles to be displayed by us.',
            'articlepublish.in' => 'Please decide if you would like articles to be displayed by us.',
            'articlelink1.regex' => 'First article link is not valid.',
            'articlelink2.regex' => 'Second article link is not valid.',
            'articlelink3.regex' => 'Third article link is not valid.',

        ];
    }

    public function saveProfile(Request $request)
    {

        $userid=Auth::id();

        $userobject=User::findOrFail($userid);

        $data=$request->all();

        $validate=$this->validate($request, $this->basicrules($data,$userobject),$this->basicmessages());
        
        if($userobject->isdjremixer)
       {
            $spotifyapi=SpotifyHelper::instance()->getSpotifySearchTokens();
            $artistitem=SpotifyHelper::instance()->getSpotifyArtistItemId($data['djartistlink']);
          $artist=SpotifyHelper::instance()->getArtist($spotifyapi,$artistitem['id']);
          if($artist->id==$artistitem['id'])
          {
              $this->insertArtist($artist,$userid);

          }
          else
          {
            $data['djartistlink']='';
            $data['remixgenre']='';
          }

       }
       else
       {
        $data['djartistlink']='';
        $data['remixgenre']='';
       }

       if($userobject->isjournalist)
       {
           
       }
       else
       {
        $data['articlelink1']='';
        $data['articlelink2']='';
        $data['articlelink3']='';
        $data['articlepublish']='';
       }


        DB::table('users')
            ->where('id', '=', $userid)
            ->update([
                'username' => $data['username'],
                'email' => $data['email'],
                'name' => $data['name'],
                'yourwebsite' => $data['yourwebsite'],
                'soundcloudlink' => $data['soundcloudlink'],
                'facebooklink' => $data['facebooklink'],
                'instagramlink' => $data['instagramlink'],
                'twitterlink' => $data['twitterlink'],
                'tiktoklink' => $data['tiktoklink'],
                'otherlink' => $data['otherlink'],
                'djartistlink' => $data['djartistlink'],
                'remixgenre' => $data['remixgenre'],
                'articlelink1' => $data['articlelink1'],
                'articlelink2' => $data['articlelink2'],
                'articlelink3' => $data['articlelink3'],
                'articlepublish' => $data['articlepublish'],
            ]);

          
            
        return redirect()->back()->with('success', ['Profile updated successfully.']);


    }

    public function insertArtist($artist,$userid,$inserttoartists=false)
    {

        if($inserttoartists)
        {
        //insert artist to artists
        DB::table('spotify_accounts_public_realartists')
        ->updateOrInsert(
    ['spid' => $artist->id],
    [
        'userid' => $userid,
    'dt' => Carbon::now()]
            );

            $last_id='';
            $last_id = DB::getPdo()->lastInsertId();

            if($last_id>0)
                    {
                        DB::table('spotify_accounts_public_realartists')
                        ->where('id', '=', $last_id)
                        ->update(['timestamp' => Carbon::now()->timestamp]);

                    }

        }


        //insert artist to spotify_items
        $imageurl='';
        if($artist->images[2]->url)
        $imageurl=$artist->images[2]->url;
        elseif($artist->images[0]->url)
        $imageurl=$artist->images[0]->url;

    DB::table('spotify_items')
        ->updateOrInsert(
    ['type' => $artist->type,
     'itemid' => $artist->id],
    [
        'name' => mb_substr($artist->name,0, 500,'UTF-8'),
        'followercount' => $artist->followers->total,
        'genres' => implode(', ', $artist->genres),
        'popularity' => $artist->popularity,
        'imageurl' => $imageurl,
        'url' => $artist->external_urls->spotify,
    'dt' => Carbon::now()]
            );

            $last_id='';
            $last_id = DB::getPdo()->lastInsertId();

            if($last_id>0)
                    {
                        DB::table('spotify_items')
                        ->where('id', '=', $last_id)
                        ->update(['timestamp' => Carbon::now()->timestamp]);


                    }


        $updatedOrInsertedRecord2='';

        $updatedOrInsertedRecord2 = DB::table('spotify_items')
           ->where('type', '=', $artist->type)
           ->where('itemid', '=', $artist->id)
           ->first();
            
           $item_id=$updatedOrInsertedRecord2->id;

           foreach ($artist->genres as $keyword_id) 
           {
           DB::table('spotify_itemkeyword_fk')
              ->updateOrInsert(
          ['item_id' => $item_id,
           'keyword_id' => $keyword_id],
                  );
            }


    }

	public function getPage(Request $request)
    {

      
        $user = DB::table('users')
					->where('id', '=', Auth::id())
					->first();

           
       


		$meta=array(

		'title' => 'Profile | '.config('myconfig.config.sitename_caps'),

		'description' => 'Profile',

		'keywords' => '',

	);
		
		return view('admin.profile', ['meta' => $meta,'user'=>$user]);
    }
	
}
