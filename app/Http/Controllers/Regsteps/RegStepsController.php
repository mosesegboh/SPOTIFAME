<?php


namespace App\Http\Controllers\Regsteps;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Helpers\SpotifyHelper;
use Carbon\Carbon;

class RegStepsController extends Controller
{
   
  public function __construct()
  {
      $this->middleware(['auth','verified','reg_step_done']);
  }

  public function secondStep() {

            $user = DB::table('users')
                        ->where('id', '=', Auth::id())
                        ->first();
              
     if($user->regstep=='1')
      return redirect()->route('admin.home');

            $meta=array(

                'title' => 'Register Now - Step2 | '.config('myconfig.config.sitename_caps'),
        
                'description' => 'Register Now - Step2',
        
                'keywords' => '',
        
            );

        
        

        return view('regsteps/regstep2', 
        [
        'meta' => $meta,
        'user'=>$user,
        ]);
    
    }


    public function thirdStep() {

            $user = DB::table('users')
                        ->where('id', '=', Auth::id())
                        ->first();
                        

            $meta=array(

                'title' => 'Register Now - Step3 | '.config('myconfig.config.sitename_caps'),

                'description' => 'Register Now - Step3',

                'keywords' => '',

            );




        return view('regsteps/regstep3', 
        [
        'meta' => $meta,
        'user'=>$user,
        ]);

    }


    public function secondStepSubmit(Request $request)
    {

        $userid=Auth::id();

        $userobject=User::findOrFail($userid);

        $data=$request->all();


        $isartist='0';
        if($data['isartist']=='1')
        $isartist='1';

        $islabel='0';
        if($data['islabel']=='1')
        $islabel='1';

        $ismanager='0';
        if($data['ismanager']=='1')
        $ismanager='1';

        $isplaylistowner='0';
        if($data['isplaylistowner']=='1')
        $isplaylistowner='1';

        $isjournalist='0';
        if($data['isjournalist']=='1')
        $isjournalist='1';

        $isdjremixer='0';
        if($data['isdjremixer']=='1') //dj/remixer
        $isdjremixer='1';


        DB::table('users')
            ->where('id', '=', $userid)
            ->update([
                'regstep' =>'3',
                'isartist' => $isartist,
                'islabel' => $islabel,
                'ismanager' => $ismanager,
                'isplaylistowner' => $isplaylistowner,
                'isjournalist' => $isjournalist,
                'isdjremixer' => $isdjremixer,
            ]);

          
     return redirect()->route('regsteps.step3');
        



    }


    /**
     * Get the basic validation rules.
     *
     * @return array
     */
    protected function basicrules(array $data,object $userobject)
    {
        return [
            'iagree' => array('accepted'),
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
            "playlistlink.*"  => $userobject->isplaylistowner ? array('required','regex:/^(https?:\/\/(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/playlist\/)([a-zA-Z0-9]+)(.*)$/') :'',
            "artistlink.*"  => ($userobject->isartist || $userobject->ismanager || $userobject->islabel) ? array('required','regex:/^(https?:\/\/(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/artist\/)([a-zA-Z0-9]+)(.*)$/') :'',
            "artistgenre.*"  => ($userobject->isartist || $userobject->ismanager || $userobject->islabel) ? array('required') :'',
        ];
    }

    protected function basicmessages()
    {
        return [
            'iagree.accepted' => 'You have to agree with our Terms of Usage.',
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
            'playlistlink.*.required' => 'You need to fill in all added playlistlink fields.',
            'playlistlink.*.regex' => 'Provided playlist link is not valid.',
            'artistlink.*.required' => 'You need to fill in all added artistlink fields.',
            'artistlink.*.regex' => 'Provided artist link is not valid.',
            'artistgenre.*.required' => 'You need to fill in all added artist genre fields.',

        ];
    }


    public function thirdStepSubmit(Request $request)
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

       
       if($userobject->isartist || $userobject->ismanager || $userobject->islabel)
       {
           
       }
       else
       {
        $data['labelname']='';
       }

       
       

       DB::table('users')
            ->where('id', '=', $userid)
            ->where('regstep', '=', '3')
            ->update([
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
                'labelname' => $data['labelname'],
            ]);


       DB::table('users')
            ->where('id', '=', $userid)
            ->where('regstep', '=', '3')
            ->update([
                'regstep' => '1',
            ]);



       $spotifyapi=SpotifyHelper::instance()->getSpotifySearchTokens();

       if($userobject->isartist || $userobject->ismanager || $userobject->islabel)
       {
            $count=0;
            foreach ($data['artistlink'] as $foreach_artistindex => $singleartistlink)
            {

                if($count>100)
                break;
                
                $artistitem=SpotifyHelper::instance()->getSpotifyArtistItemId($singleartistlink);

                $artist=SpotifyHelper::instance()->getArtist($spotifyapi,$artistitem['id']);
                
                if($artist->id!='')
                {
                    //check if exists
                    $row_ar_check = DB::table('spotify_accounts_public_realartists')
                    ->where('spid', '=', $artist->id)
                    ->where('userid', '!=', $userid)
                    ->limit(1)
                    ->get();

                    foreach ($row_ar_check as $row_ar) {
                        $row_results_ar[]=$row_ar;
                    }

                    if(!empty($row_results_ar))
                    {
                    return redirect()->back()->withInput()->with('error', ['The following artist is already taken, contact us if you think this is a mistake: '.$artist->id]);
                    }

                    $thegenre='';
                    if($data['artistgenre'][$foreach_artistindex]!='')
                    $thegenre=$data['artistgenre'][$foreach_artistindex];
                    $this->insertArtist($artist,$userid,1,$thegenre);

                }

                $count++;
            }
           
       }


       if($userobject->isplaylistowner)
       {
            $count=0;
            foreach ($data['playlistlink'] as $singleplaylistlink)
            {
                if($count>100)
                break;

                $playlistitem=SpotifyHelper::instance()->getSpotifyPlaylistItemId($singleplaylistlink);

                $theplaylist=SpotifyHelper::instance()->getPlaylist($spotifyapi,$playlistitem['id']);

                
                if($theplaylist->id!='')
                {
                    //check if exists
                    $row_pl_check = DB::table('spotify_accounts_public_realplaylists')
                    ->where('spid', '=', $theplaylist->id)
                    ->where('userid', '!=', $userid)
                    ->limit(1)
                    ->get();

                    foreach ($row_pl_check as $row_pl) {
                        $row_results_pl[]=$row_pl;
                    }

                    if(!empty($row_results_pl))
                    {
                    return redirect()->back()->withInput()->with('error', ['The following playlist is already taken, contact us if you think this is a mistake: '.$theplaylist->id]);
                    }

                   
                    //insert playlist to playlists
                    DB::table('spotify_accounts_public_realplaylists')
					->updateOrInsert(
				['spid' => $theplaylist->id],
				[
					'userid' => $userid,
				'dt' => Carbon::now()]
                        );

                        $last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_accounts_public_realplaylists')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
								}

                    //insert playlist to spotify_items

                    $imageurl='';
                    if($theplaylist->images[2]->url)
                    $imageurl=$theplaylist->images[2]->url;
                    elseif($theplaylist->images[0]->url)
                    $imageurl=$theplaylist->images[0]->url;


                    DB::table('spotify_items')
					->updateOrInsert(
				['type' => $theplaylist->type,
				 'itemid' => $theplaylist->id],
				[
					'name' => mb_substr($theplaylist->name,0, 500,'UTF-8'),
					'followercount' => $theplaylist->followers->total,
					'imageurl' => $imageurl,
					'url' => $theplaylist->external_urls->spotify,
					'ownerurl' => $theplaylist->owner->external_urls->spotify,
					'ownername' => mb_substr($theplaylist->owner->display_name,0, 500,'UTF-8'),
					'description' => $theplaylist->description,
					'collaborative' => $theplaylist->collaborative,
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

                }

                $count++;
            }
       }
       

       

            return redirect()->route('admin.home');
        
    }


    public function insertArtist($artist,$userid,$inserttoartists=false,$thegenre='')
    {
        
        if($inserttoartists)
        {

        //insert artist to artists
        DB::table('spotify_accounts_public_realartists')
        ->updateOrInsert(
    ['spid' => $artist->id],
    [
        'genre'=>$thegenre,
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



    
}