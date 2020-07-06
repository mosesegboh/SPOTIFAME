<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
   
  public function __construct()
  {
      $this->middleware(['guest']);
  }

	function getPage() {


    $theresultset=DB::table('spotify_statistics_homepage AS t1')
    ->select('t1.*')
    ->orderByRaw('t1.id ASC')
    ->get();

    $homepagestats=new \stdClass();
    foreach ($theresultset as $theresultset_s)
    {
        $thename=$theresultset_s->realname;
        $homepagestats->$thename=$theresultset_s->realvalue;
    }

    $genre_results=DB::table('spotify_statistics_home_genres as t1')
                    ->select('t1.*')
                    ->orderByRaw('t1.active DESC')
                    ->get();

                    $s_count=0;
                    foreach ($genre_results as $genre_results_s)
                    {

                        $genre_triplet=explode('|',$genre_results_s->variation);
                        $triplet=array();
                        foreach ($genre_triplet as $genre_triplet_s)
                        {
                          
                          $genre_results2=DB::table('spotify_genres as t1')
                          ->select('t1.*')
                          ->where('t1.id','=',$genre_triplet_s)
                          ->limit(1)
                          ->get();
                          
                          
                          foreach ($genre_results2 as $genre_results_s2)
                            {
                              $single_genre['name']=ucwords($genre_results_s2->name);
                              $single_genre['item_count']=$genre_results_s2->item_count;
                              $triplet[]=$single_genre;
                            }

                            

                        }
                        

           $genreresultset[$s_count]=$triplet;

               $s_count++;

                    }

    //First replace every \ with a double slash \\ and then every quote" with a \"
    $genreresultset = json_encode($genreresultset);
    $genreresultset= preg_replace("_\\\_", "\\\\\\", $genreresultset);
    $genreresultset = preg_replace("/\"/", "\\\"", $genreresultset);


    $meta=array(

      'title' => 'Advanced artist management and promotion system | '.config('myconfig.config.sitename_caps'),
  
      'description' => 'Spotifame is a global promotion and management tool for artists, playlist editors, journalists, managers, and labels. Spotifame is used by thousands of major artists and thousands of worldwide playlist editors.',
  
      'keywords' => 'spotify, playlist, playlist curator, playlist editor, artist promotion, music aggregator',

      'abstract' => 'music aggregator, artist promotion, playlist editor, playlist curator, playlist, spotify',

      'image' => config('myconfig.config.server_url').'images/largeshare.png',
  
    );
    

    return view('home', 
    [
    'meta' => $meta,
    'choice_index'=>'active',
    'homepagestats'=>$homepagestats,
    'genreresultset'=>$genreresultset
    ]);
    
  }



    
}