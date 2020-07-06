<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper as SpotifyHelper;

class GroupPageController extends Controller
{
    

    public function __construct()
    {

		$this->middleware('auth');

        $this->generateConfig();

	$this->settings=$this->getSettings();

	$this->rangeslidervalues=array(
		$this->settings['sp_range_min'],
		$this->settings['sp_range_max'],
		$this->settings['sp_range_break']

	);

	}

    public function getPage(Request $request, $groupid = null)
    {


$ipAddress = $_SERVER['REMOTE_ADDR'];
$userid = Auth::id();

if($groupid=='')
return redirect('notfound');


        if ($request->input('pagenum'))
		$offset = ($request->input('pagenum')-1)*$this->settings['sp_perpage'];
		else
        $offset = '0';
        
        if($request->input('orderby') =='' || !$request->input('orderby'))
		$orderbyget='';
		else
		$orderbyget=$request->input('orderby');

		if($orderbyget=='added')
		$this->orderby='t1.timestamp DESC';
		elseif($orderbyget=='followers')
		$this->orderby='t1.followercount DESC';
		elseif($orderbyget=='name')
		$this->orderby='t1.name ASC';
		else
		$this->orderby='t1.id ASC'; //default


$group_get = DB::table('spotify_groups as t1')
					->select('t1.*')
					->where('t1.id', '=', $groupid)
					->limit(1)
					->get();


					foreach ($group_get as $row) {
						$thegroup[] = $row;
					}

					if(is_null($thegroup) || 
					empty($thegroup))
					{
						return redirect('notfound');
					}

					$thegroup=$thegroup[0];
                    
              if($thegroup->searchstring!='' && $thegroup->item_count>0)
              {
                  $searchstrings= explode('|',$thegroup->searchstring);
                  $s_c=0;
                  $processedresults=array();
                  foreach($searchstrings as $searchstrings_s)
                  {
                    $processedresults[$s_c]=new \stdClass();
                        $thesearchstring=base64_decode($searchstrings_s);

                        $thesearchstring_expl=explode('_',$thesearchstring);

                        $searchtype=$thesearchstring_expl[0];
                        $searchname=$thesearchstring_expl[1];
                        
                        $inputfolmin='';
                        $inputfolmax='';
                        $inputfolmin=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[2],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
                        $inputfolmax=SpotifyHelper::instance()->transformRangeValue($thesearchstring_expl[3],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);

                        $searchgenrestring=$thesearchstring_expl[4];

                        $claimedshow=$thesearchstring_expl[5];
                        $claimedshow2=$thesearchstring_expl[6];
                        $notclaimedshow=$thesearchstring_expl[7];
                        $unknownshow=$thesearchstring_expl[8];

                        $orderbyget=$thesearchstring_expl[9];

                        $artistswithoutgenres=$thesearchstring_expl[10];
                        if($artistswithoutgenres=='1')
                        $searchgenrestring='';

                        $hidespotifyowned=$thesearchstring_expl[11];


                        if($searchtype=='playlist')
                        {
                            $artistswithoutgenres=0;
                            $searchgenrestring='';
                                $claimedshow=0;
                                $claimedshow2=0;
                                $notclaimedshow=0;
                                $unknownshow=0;
                        }

                        if($searchtype=='artist')
                        {
                            $hidespotifyowned=0;
                        }


                        $processedresults[$s_c]->searchtype=$searchtype;

                        $processedresults[$s_c]->url='?pagenum=1&searchset=1&searchtype='.$searchtype;
                        $processedresults[$s_c]->searchstring='';
                    
                        if($searchname!='')
                        {
                        $processedresults[$s_c]->url.='&title='.$searchname;
                        $processedresults[$s_c]->searchstring.='Query:'.$searchname.', ';
                        }
                                        
                        if($searchgenrestring!='')
                        {
                        $processedresults[$s_c]->url.='&genres='.$searchgenrestring;
                        $processedresults[$s_c]->searchstring.='Genres:'.str_replace(',',', ',$searchgenrestring).', ';
                        }
                    
                        $processedresults[$s_c]->url.='&followers='.$thesearchstring_expl[2].';'.$thesearchstring_expl[3];
                        $processedresults[$s_c]->searchstring.='Followers:'.$inputfolmin.'-'.$inputfolmax;
                    
                        
                    
                        if($claimedshow)
                        {
                            $processedresults[$s_c]->url.='&claimedshow=on';
                            $processedresults[$s_c]->searchstring.=', Claimed: yes';
                        }
                    
                        if($claimedshow2)
                        {
                            $processedresults[$s_c]->url.='&claimedshow2=on';
                            $processedresults[$s_c]->searchstring.=', Claimed (changed): yes';
                        }
                    
                        if($notclaimedshow)
                        {
                            $processedresults[$s_c]->url.='&notclaimedshow=on';
                            $processedresults[$s_c]->searchstring.=', Not claimed: yes';
                    
                        }
                    
                        if($unknownshow)
                        {
                            $processedresults[$s_c]->url.='&unknownshow=on';
                            $processedresults[$s_c]->searchstring.=', Unknown: yes';
                    
                        }

                        if($artistswithoutgenres=='1')
                        {
                            $processedresults[$s_c]->url.='&artistswithoutgenres=on';
                            $processedresults[$s_c]->searchstring.=', Artist without genres: yes';
                    
                        }

                        if($hidespotifyowned=='1')
                        {
                            $processedresults[$s_c]->url.='&hidespotifyowned=on';
                            $processedresults[$s_c]->searchstring.=', Hide spotify owned: yes';
                    
                        }

                            if($orderbyget=='added')
                            {
                                $processedresults[$s_c]->url.='&orderby=added';
                                $processedresults[$s_c]->searchstring.=', Order by: Added';
                        
                            }
                            elseif($orderbyget=='followers')
                            {
                                $processedresults[$s_c]->url.='&orderby=followers';
                                $processedresults[$s_c]->searchstring.=', Order by: Followers';
                        
                            }
                            elseif($orderbyget=='name')
                            {
                                $processedresults[$s_c]->url.='&orderby=name';
                                $processedresults[$s_c]->searchstring.=', Order by: Name';
                        
                            }
                            else
                            {
                                $processedresults[$s_c]->url.='&orderby=';
                                $processedresults[$s_c]->searchstring.=', Order by: Spotify';
                            }

                        $s_c++;
                  }

                  $thegroup->searchstrings=$processedresults;


              } 
           

                    if($thegroup->item_count>0)
                    {

  if($thegroup->type=='track')
    {
                        $theresultset = 
					DB::table('spotify_accounts_auth_realtracks as t1')
                    ->select('t1.*','t3.popularity','t3.id AS realitemid')
                    ->leftJoin('spotify_items AS t3', function($join)
							{
							$join->on('t3.itemid', '=', 't1.spid');
                            })
                    ->leftJoin('spotify_groups_'.$thegroup->type.'s_fk AS t10', function($join) use ($thegroup)
							{
                            $join->on('t10.item_id', '=', 't3.id');
                            })
                    ->where('t10.group_id', '=', $thegroup->id)
					->orderByRaw($this->orderby)
					->offset($offset)
					->limit($this->settings['sp_perpage'])
					->get();
                    
                    
                    $s_c=0;
					foreach($theresultset as $theresultset_s)
								{


                                    $theresultset2 = 
                    DB::table('spotify_accounts_auth_realplaylists AS t3')
                    ->select('t4.*','t3.spid',)
                    ->leftJoin('spotify_items AS t4', function($join)
							{
                            $join->on('t3.spid', '=', 't4.itemid');
                            $join->where('t4.type','=', 'playlist');
                            })
                    ->leftJoin('spotify_trackplaylist_fk AS t5', function($join)
							{
                            $join->on('t5.playlist_id', '=', 't3.id');
                            })
                    ->where('t5.track_id','=',$theresultset_s->id)
                    ->whereNotNull('t4.name')
					->orderByRaw('t3.id DESC')
                    ->get();
                    

                    $theresultset_s->playlists=$theresultset2;


									$theresultset[$s_c]=$theresultset_s;
									$s_c++;
								}

    }


    if($thegroup->type=='artist' || $thegroup->type=='playlist')
    {
                        $theresultset = 
                        DB::table('spotify_items as t1')
                        ->select('t1.*')
                        ->leftJoin('spotify_groups_'.$thegroup->type.'s_fk AS t2', function($join) use ($thegroup)
							{
                            $join->on('t2.item_id', '=', 't1.id');
                            })
                        ->where('t2.group_id', '=', $thegroup->id)
                        ->orderByRaw($this->orderby)
                        ->offset($offset)
                        ->limit($this->settings['sp_perpage'])
                        ->get();


                        $s_c=0;
					foreach($theresultset as $theresultset_s)
								{

									$theresultset[$s_c]->followers=new \stdClass();
									$theresultset[$s_c]->followers->total=$theresultset_s->followercount;

									$theresultset[$s_c]->external_urls=new \stdClass();
									$theresultset[$s_c]->external_urls->spotify=$theresultset_s->url;

									$theresultset[$s_c]->images=array();
									$theresultset[$s_c]->images[2]=new \stdClass();
									$theresultset[$s_c]->images[2]->url=$theresultset_s->imageurl;

									$theresultset[$s_c]->mydbid=$theresultset[$s_c]->id;
									$theresultset[$s_c]->id=$theresultset[$s_c]->itemid;

									if($thegroup->type=='playlist')
									{
									$theresultset[$s_c]->owner=new \stdClass();
									$theresultset[$s_c]->owner->external_urls=new \stdClass();
									$theresultset[$s_c]->owner->external_urls->spotify=$theresultset[$s_c]->ownerurl;
									$theresultset[$s_c]->owner->display_name=$theresultset[$s_c]->ownername;
									}


									$s_c++;
								}


    }

								$this->searchresults=$theresultset;

                            $item_count=$thegroup->item_count;


                        $paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
                            foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
                            {
                                $$paginationarray_key=$paginationarray_value;
                            }

                    }


		$meta=array(

		'title' => 'Group: '.$thegroup->name.' | '.config('myconfig.config.sitename_caps'),

		'description' => 'Group: '.$thegroup->name,

		'keywords' => '',

	);


		return view('admin/grouppage', [
            'searchresults'=>$this->searchresults,
            'meta' => $meta,
            'item_count'=> number_format($item_count),
            'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
            'pagination'=>$pagination,'paginationdisplay'=>$paginationdisplay,
			'thegroup' => $thegroup,
		]);
		



	}

	
	
	
}
