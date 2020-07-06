<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'type', 'artistname', 'soundcloudlink', 
        'isartist', 'islabel', 'ismanager', 'isplaylistowner', 'isjournalist',
        'isdjremixer', 'generated', 'note', 'regstep', 'facebooklink', 'instagramlink', 'twitterlink',
        'tiktoklink', 'otherlink', 'djartistlink', 'remixgenre', 'articlepublish', 'articlelink1', 'articlelink2',
        'articlelink3', 'labelname', 'yourwebsite'
  
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    const ADMIN_TYPE = 'admin';
    const EDITOR_TYPE = 'editor';
    const ASSISTANT_TYPE = 'assistant';


    const DEFAULT_TYPE = 'default';
    const PLAYLISTER_TYPE = 'playlister';
    const ARTISTMANAGER_TYPE = 'artistmanager';
    const PROMOTER_TYPE = 'promoter';
    const FOLLOWER_TYPE = 'follower';
    
    public function isAdmin()    {        
		return $this->type === self::ADMIN_TYPE;    
	}
	public function isEditor()    {        
		return $this->type === self::EDITOR_TYPE;    
    }
    public function isAssistant()    {        
		return $this->type === self::ASSISTANT_TYPE;    
    }

    public function isUser()    {    
        
       $type_expl=explode('|',$this->type);

        if(
        in_array(self::DEFAULT_TYPE,$type_expl) || 
        in_array(self::PLAYLISTER_TYPE,$type_expl) || 
        in_array(self::ARTISTMANAGER_TYPE,$type_expl) || 
        in_array(self::PROMOTER_TYPE,$type_expl) || 
        in_array(self::FOLLOWER_TYPE,$type_expl)
        )
        {
        return true;    
        }
    }

    public function isDefault()    {    
        
        $type_expl=explode('|',$this->type);
 
         if(
         in_array(self::DEFAULT_TYPE,$type_expl)
         )
         {
         return true;    
         }
     }

     public function isPlaylister()    {    
        
        $type_expl=explode('|',$this->type);
 
         if(
         in_array(self::PLAYLISTER_TYPE,$type_expl)
         )
         {
         return true;    
         }
     }

     public function isArtistmanager()    {    
        
        $type_expl=explode('|',$this->type);
 
         if(
         in_array(self::ARTISTMANAGER_TYPE,$type_expl)
         )
         {
         return true;    
         }
     }

     public function isPromoter()    {    
        
        $type_expl=explode('|',$this->type);
 
         if(
         in_array(self::PROMOTER_TYPE,$type_expl)
         )
         {
         return true;    
         }
     }

     public function isFollower()    {    
        
        $type_expl=explode('|',$this->type);
 
         if(
         in_array(self::FOLLOWER_TYPE,$type_expl)
         )
         {
         return true;    
         }
     }

     public function hasType($type)  {


        return User::where('type', 'like', '%'.$type .'%')->get();
     
    }

    public function regStep()  {

        return $this->regstep;    
     
    }
    
}
