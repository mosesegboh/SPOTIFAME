
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAssistant())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">

            <h2 class="text-center mb-3"><u>Browse Spotify</u></h2>
                         <!--<div class="row">
                          <div class="col-12">-->
                            <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">

                                <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">

                                <div class="form-group d-flex">
                              <input name="title" id="title" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Type in something *" value="{{ urldecode(request()->input('title')) }}" autocomplete="on">
                              <div class="input-group-btn">
                                <div class="btn-group" role="group">
                              <button id="openadvsearch"{{ urldecode(request()->input('advopen'))=='1' ? ' data-clicks="1"' :''}} type="button" class="btn btn-outline-success pl-2 pr-2" data-toggle="collapse" href="#collapse-8" aria-expanded="false" aria-controls="collapse-8">
                               <i class="mdi mdi-filter"></i>
                                  </button>  
                              <button type="submit" class="btn btn-primary ml-0">Search</button>
                            </div>
                                </div>
                     </div>

                     @if ($responsestatus)
                        @if($responsestatus=='429')
                        <!--<div class="alert alert-danger">Information: This was a hard search with a lot of results to sort, that is why it took so long! </div>-->
                        @elseif($responsestatus!='200' && auth()->user()->isAdmin())
                        <div class="alert alert-danger">Message to developer: response status: {{$responsestatus}}</div>
                        @endif      
                    @endif  
<!-- Advanced search -->


    <div id="collapse-8" class="collapse{{ urldecode(request()->input('advopen'))=='1' ? ' show' :''}}" role="tabpanel" aria-labelledby="heading-8">
        <div class="card">
        <div class="card-body">
          
            <h4 class="mb-4">Advanced Search:</h4>


    <input type="hidden" class="" id="advopen" name="advopen" value="{{ urldecode(request()->input('advopen')) }}" autocomplete="off">


    <div class="spotifysearchpart position-relative p-4 mb-3 border border-secondary"> <!-- spotify search part -->

      <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Spotify Api Filters</span>

    <div class="form-group row">
      <label for="searchtype" class="lineheight24rem">Type:</label>
      <div class="col-sm-9">
          <select class="form-control border-secondary" id="searchtype" name="searchtype" autocomplete="off">
              <option value="artist"{{ urldecode(request()->input('searchtype'))=='artist' || urldecode(request()->input('searchtype'))=='' ? ' selected="selected"' :''}}>artist</option>
              <option value="album"{{ urldecode(request()->input('searchtype'))=='album' ? ' selected="selected"' :''}}>album</option>
              <option value="playlist"{{ urldecode(request()->input('searchtype'))=='playlist' ? ' selected="selected"' :''}}>playlist</option>
              <option value="track"{{ urldecode(request()->input('searchtype'))=='track' ? ' selected="selected"' :''}}>track</option>
              <option value="show"{{ urldecode(request()->input('searchtype'))=='show' ? ' selected="selected"' :''}}>show</option>
              <option value="episode"{{ urldecode(request()->input('searchtype'))=='episode' ? ' selected="selected"' :''}}>episode</option>
          </select>
      </div>
   </div>
   
   <div id="genreswrap" class="form-group row">
    <label for="genres" class="lineheight24rem">Genres:</label>
      <div class="col-sm-9">
        <div class="card-body pl-2 pt-1 pb-1 pr-2">
          <input name="genres" id="genres" value="{{ urldecode(request()->input('genres')) }}" autocomplete="off">
        </div>
      </div>
  </div>

  
          @if(!empty($markets))
          <div class="form-group row">
                <label for="marketselect" class="lineheight24rem" >Market:</label>
                <div class="col-sm-9">
                    <select class="form-control border-secondary" id="marketselect" name="marketselect" autocomplete="off">
                      <option value=""{!! $markets[urldecode(request()->input('marketselect'))] ? '' :' selected="selected"'!!}>Choose a Market</option>
                        @foreach ($markets as $countrysign => $countryname)
                        <option value="{{ $countrysign }}"{!! urldecode(request()->input('marketselect'))==$countrysign ? ' selected="selected"' :''!!}>{{ $countrysign }}</option>
                        @endforeach
                    </select>
                </div>
             </div>
             @endif

<!--

             <div class="form-group row">
                <label for="artist" class="lineheight24rem">Artist</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control border-secondary" id="artist" name="artist" placeholder="" value="{{ urldecode(request()->input('artist')) }}" autocomplete="off">
                </div>
              </div>

              <div class="form-group row">
                <label for="track" class="lineheight24rem">Track</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control border-secondary" id="track" name="track" placeholder="" value="{{ urldecode(request()->input('track')) }}" autocomplete="off">
                </div>
              </div>

              <div class="form-group row">
                <label for="album" class="lineheight24rem">Album</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control border-secondary" id="album" name="album" placeholder="" value="{{ urldecode(request()->input('album')) }}" autocomplete="off">
                </div>
              </div>

-->

  <div id="yearfromtowrap" class="form-group row">

    <label class="col-6 col-lg-4 col-form-label maxwidth136 lineheight04rem yearslabelmobile">Between Years:</label>
    <div class="input-group input-daterange">
      
      <input type="text" id="yearfrom" name="yearfrom" class="form-control" value="{{ urldecode(request()->input('yearfrom')) ? urldecode(request()->input('yearfrom')) :'1900' }}" autocomplete="off">
      <div class="input-group-text fromtosign">-</div>
      <input type="text" id="yearto" name="yearto" class="form-control" value="{{ urldecode(request()->input('yearto')) ? urldecode(request()->input('yearto')) : now()->year }}" autocomplete="off">
  </div>



  </div>



             <div id="isnewwrap" class="form-check">
                               <label class="form-check-label">
                                 <input name="isnew" id="isnew" type="checkbox" class="form-check-input"{!! urldecode(request()->input('isnew'))=='on' ? ' checked="checked"' :''!!} autocomplete="off">
                                Is new
                               <i class="input-helper"></i></label>
             </div>


        
        </div> <!-- spotify search part -->

     

        <div class="spotifysearchpart position-relative p-4 border border-secondary oursearchpart"> <!-- our search part -->
            
          <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Our Filters</span>

             <div id="followerwrap" class="form-group slider-wrap mt-4 mb-4">
                <p class="">Followers:</p>
                <input type="text" id="followers" name="followers" value="" autocomplete="off">


                <label class="col-6 col-lg-4 col-form-label lineheight04rem maxwidth136 yearslabelmobile">Or Type in:</label>
     <div class="input-group">
                  
                  <input type="text" id="fromfollowers" class="form-control" value="{{ urldecode(request()->input('followers')) ? \App\Helpers\SpotifyHelper::instance()->transformRangeValue(explode(';',urldecode(request()->input('followers')))[0],$rangeslidervalues[2],$rangeslidervalues[1]) : $rangeslidervalues[0] }}" autocomplete="off">
                  <div class="input-group-text fromtosign">-</div>
                  <input type="text" id="tofollowers" class="form-control" value="{{ urldecode(request()->input('followers')) ? \App\Helpers\SpotifyHelper::instance()->transformRangeValue(explode(';',urldecode(request()->input('followers')))[1],$rangeslidervalues[2],$rangeslidervalues[1]) : $rangeslidervalues[1] }}" autocomplete="off">
     </div>
    

              </div>

            
         <div id="hidespotifyownedwrap">
              <div class="form-check d-inline-block">
                <label class="form-check-label">
                  <input name="hidespotifyowned" id="hidespotifyowned" type="checkbox" class="form-check-input"{!! urldecode(request()->input('hidespotifyowned'))=='on' ? ' checked="checked"' :''!!} autocomplete="off">
                 Hide Spotify Owned Playlists
                <i class="input-helper"></i></label>

                <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="If you search with this: It will hide spotify owned playlists."></i>
            </div>
          </div>


          <div id="hideclaimedwrap">
            <div class="form-check d-inline-block">
              <label class="form-check-label">
                <input name="hideclaimed" id="hideclaimed" type="checkbox" class="form-check-input"{!! urldecode(request()->input('hideclaimed'))=='on' ? ' checked="checked"' :''!!} autocomplete="off">
              Hide Known Claimed Artists
              <i class="input-helper"></i></label>

              <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="If you search with this: known claimed profiles are hidden, do note that for the full usage of this feature you'd better check local database cause there are more processed profiles, here we might have few results checked for claimed state!"></i>

            </div>

          </div>

          <div id="requestwoutcachewrap">
              <div class="form-check d-inline-block">
                <label class="form-check-label">
                  <input name="requestwoutcache" id="requestwoutcache" type="checkbox" class="form-check-input"{!! urldecode(request()->input('requestwoutcache'))=='on' ? ' checked="checked"' :''!!} autocomplete="off">
                 Without Cache
                <i class="input-helper"></i></label>
                
                <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="If you search with this: results are requested from Spotify again and the specific search is put in to the searchqueue to be processed again. (if it is allowed in settings!)"></i>
                </div>
              </div>

            </div> <!-- our search part -->

              <button type="submit" class="btn btn-primary mt-2 float-right">Search</button>

      </div>
    </div>

           


    </div>
<!-- Advanced search-->

                            </form>
                          </div>

                    

                

      <div id="searchresults" class="col-lg-12 px-0">
           <div class="col-12 mb-5 mt-5">
                            <h2>Search Result For<u class="ml-2">"{{ urldecode(request()->input('title')) }}"</u></h2>
                            <p class="text-muted">{!! $paginationdisplay !!}

                            @if ($searchresults[0]->type=='artist' || $searchresults[0]->type=='playlist')
                              @if($inprogress=='0')
                              <span class="text-success pl-2">(All results processed.)</span>
                              @else
                              <span class="text-danger pl-2">(More results in progress...)</span>
                              @endif
                                @if ($thecacheid>0)
                                <a href="{{ config('myconfig.config.server_url')  }}admin/searchesinprogress?getcurrentid={{ $thecacheid }}" target="_blank">More details</a>
                                @endif
                            @endif
                            </p>
                            @if ($searchresults[0]->type=='artist' || $searchresults[0]->type=='playlist')
                            <p class="text-muted">
                            <span class="text-warning">Results are from:<u class="pl-1">{{ $iscache ? 'CACHE' : 'SPOTIFY'}}</u></span>
                            </p>
                            @endif

                          </div>

                    @if($searchresults)
          <div class="col-lg-12 grid-margin">
                          <div class="card results">
                            <div class="card-body">

                          <div class="table-responsive">
                            <table class="table table-hover resulttable">
                              <thead>
                                <tr>
                                  <th class="text-center width100">Name</th>

                                  @if ($searchresults[0]->type=='artist' || $searchresults[0]->type=='playlist')
                                  <th class="text-center width120">Followers</th>
                                  @endif

                                  @if ($searchresults[0]->type=='artist')
                                  <th class="text-center width120">State</th>
                                  @endif

                                  @if ($searchresults[0]->type=='artist' || $searchresults[0]->type=='playlist')
                                  <th class="text-center width120">Link</th>
                                  @endif

                                  @if ($searchresults[0]->type=='track' || $searchresults[0]->type=='episode') 
                                  <th class="text-center width120">Duration</th>
                                  @endif

                                  @if ($searchresults[0]->type=='track')
                                  <th class="text-center width120">Album</th>
                                  @endif

                                  @if ($searchresults[0]->type=='track' || $searchresults[0]->type=='album') 
                                  <th class="text-center width120">Artist</th>
                                 @endif

                                 @if ($searchresults[0]->type=='episode')
                                 <th class="text-center width120">Show</th>
                                @endif

                                @if ($searchresults[0]->type=='episode' || $searchresults[0]->type=='album')
                                <th class="text-center width120">Release Date</th>
                                 @endif

                                 @if ($searchresults[0]->type=='track')
                                 <th class="text-center width140">Album Release Date</th>
                                 @endif


                                 @if ($searchresults[0]->type=='show')
                                 <th class="text-center width120">Show Type</th>
                                 @endif

                                 @if ($searchresults[0]->type=='show')
                                 <th class="text-center width120">Publisher</th>
                                 @endif

                                 @if ($iscache && ($searchresults[0]->type=='artist' || $searchresults[0]->type=='playlist'))
                                 <th class="text-center width120">Added to cache<br>(GMT)</th>
                                 @endif

                                @if ($searchresults[0]->type=='playlist')
                                 <th class="text-center width120">Owner</th>
                                @endif

                                @if ($searchresults[0]->type=='artist')
                                  <th class="text-center width200">Genres</th>
                                  @endif

                                  @if ($searchresults[0]->type=='artist')
                                  <th class="text-center width100">Distributor</th>
                                  @endif

                                </tr>
                              </thead>
                              <tbody>

                                @if($searchresults)
                            @foreach ($searchresults as $searchresult)
                                <tr {!! $searchresult->type=='artist' ? ($searchresult->claimed=='1' ? 'class="successful-bg"' : ($searchresult->claimed=='2' ? 'class="problem-bg"' : ( $searchresult->claimed=='3' ? 'class="wesetitclaimed-bg"' : 'class="waiting-bg"'))) :'' !!}>

                                  <td class="text-center">@if ($searchresult->images[2]->url)
                                    <img class="h-auto" src="{{ $searchresult->images[2]->url }}" />
                                    @elseif($searchresult->images[0]->url)
                                    <img class="h-auto" src="{{ $searchresult->images[0]->url }}" />
                                    @endif
                                    @if ($searchresult->album->images[2]->url)
                                    <img class="h-auto" src="{{ $searchresult->album->images[2]->url }}" />
                                    <br>
                                    @endif
                                    <p>
                                      <a class="d-inline-block h4 fontsize14" href="{{ config('myconfig.config.server_url')  }}admin/artist/{{ $searchresult->mydbid }}" target="_blank">{{ $searchresult->name }}</a>
                                    </p>
                                  </td>

                                  
                                  @if ($searchresult->type=='artist' || $searchresult->type=='playlist')
                                  <td class="text-center">{{ number_format($searchresult->followers->total) }}</td>
                                  @endif

                                  @if ($searchresult->type=='artist')
                                  <td class="text-center">{{ $searchresult->claimed=='1' ? 'Claimed' : ($searchresult->claimed=='2' ? 'Not Claimed' : ( $searchresult->claimed=='3' ? 'Claimed (changed)' : 'Unknown')) }}</td>
                                  @endif

                                  @if ($searchresult->type=='artist' || $searchresult->type=='playlist')
                                  <td class="text-center"><a class="d-inline-block h3" href="{{ $searchresult->external_urls->spotify }}" target="_blank"><i class="mdi mdi-link"></i></td>
                                  @endif
                                    
                                  @if ($searchresult->type=='track' || $searchresult->type=='episode') 
                                  <td class="text-center">{{$searchresult->duration}}</td>
                                  @endif

                                  @if ($searchresult->type=='track')
                                  @if ($searchresult->album->external_urls->spotify !='')
                                  <td class="text-center"><a href="{{ $searchresult->album->external_urls->spotify }}" target="_blank">{{ $searchresult->album->name ? $searchresult->album->name : 'Unkown' }}</a></td>
                                  @else
                                  <td class="text-center">{{ $searchresult->album->name ? $searchresult->album->name : 'Unkown' }}</td>
                                  @endif
                                 @endif

                                 @if ($searchresult->type=='track' || $searchresult->type=='album') 
                                  @if ($searchresult->artists[0]->external_urls->spotify !='')
                                  <td class="text-center"><a href="{{ $searchresult->artists[0]->external_urls->spotify }}" target="_blank">{{ $searchresult->artists[0]->name ? $searchresult->artists[0]->name : 'Unkown' }}</a></td>
                                  @else
                                  <td class="text-center">{{ $searchresult->artists[0]->name ? $searchresult->artists[0]->name : 'Unkown' }}</td>
                                  @endif
                                 @endif

                                 @if ($searchresults[0]->type=='episode')
                                 @if ($searchresult->show->external_urls->spotify !='')
                                 <td class="text-center"><a href="{{ $searchresult->show->external_urls->spotify }}" target="_blank">{{ $searchresult->show->name ? $searchresult->show->name : 'Unkown' }}</a></td>
                                 @else
                                 <td class="text-center">{{ $searchresult->show->name ? $searchresult->show->name : 'Unkown' }}</td>
                                 @endif
                                @endif


                                @if ($searchresult->type=='episode' || $searchresult->type=='album')
                                <td class="text-center">{{ date('d/m/Y', strtotime($searchresult->release_date)) }}</td>
                                 @endif

                                 @if ($searchresult->type=='track')
                                 <td class="text-center">{{ date('d/m/Y', strtotime($searchresult->album->release_date)) }}</td>
                                 @endif


                                 @if ($searchresult->type=='show')
                                  <td class="text-center">{{$searchresult->media_type}}</td>
                                 @endif

                                 @if ($searchresult->type=='show')
                                 <td class="text-center">{{$searchresult->publisher}}</td>
                                 @endif


                                 @if ($iscache && ($searchresults[0]->type=='artist' || $searchresults[0]->type=='playlist'))
                                 <td class="text-center">{{ date('d/m/Y H:i', $searchresult->timestamp) }}</td>
                                 @endif

                                 @if ($searchresult->type=='playlist')
                                  @if ($searchresult->owner->external_urls->spotify !='')
                                  <td class="text-center"><a href="{{ $searchresult->owner->external_urls->spotify }}" target="_blank">{{ $searchresult->owner->display_name ? $searchresult->owner->display_name : 'Unkown' }}</a></td>
                                @else
                                <td class="text-center">{{ $searchresult->owner->display_name ? $searchresult->owner->display_name : 'Unkown' }}</td>
                                @endif
                                @endif

                                @if ($searchresults[0]->type=='artist')
                                  @if (is_array($searchresult->genres))
                                    <td class="text-center">{{ $searchresult->genres ? implode(', ', $searchresult->genres) :'-' }}</td>
                                    @else
                                    <td class="text-center">{{ $searchresult->genres ? $searchresult->genres :'-' }}</td>
                                    @endif
                                  @endif

                                  @if ($searchresult->type=='artist')
                                  <td class="text-center">
                                  @if ($searchresult->distributorurl !='')
                                  <a href="{{ $searchresult->distributorurl }}" target="_blank">{{ $searchresult->distributorname ? $searchresult->distributorname : 'Unkown' }}</a>
                                  @else
                                  {{ $searchresult->distributorname ? $searchresult->distributor : 'Unkown' }}
                                  @endif
                                  </td>
                                 @endif

                                </tr>
                                




                                @endforeach
                            @endif

                              </tbody>
                            </table>
                          </div>

                        </div>
                      </div>
                    
                    </div>

                    @endif
                    
       
                          <nav class="col-12" aria-label="Page navigation">
                            {!! $pagination !!}
                          </nav>

            
           </div>  
                        
                        

                        </div>
                      </div>
                    </div>
</div>
<!-- content-wrapper ends -->

<script>
var item_count='{{$item_count}}';
var followersinput='{{urldecode(request()->input('followers'))}}';
var nowdate=moment().format('DD/MM/YYYY');
var nowdateyear=moment().format('YYYY');

var yearfromstart=new Date('{{$yearfromstartyear}}', 01, 01);

var yeartostart=new Date('{{$yeartostartyear}}', 01, 01);

var minrangevalue=parseInt('{{$rangeslidervalues[0]}}');
var maxrangevalue=parseInt('{{$rangeslidervalues[1]}}');

var rangebreakpoint=parseInt('{{$rangeslidervalues[2]}}');


</script>

@endif
@endif
@endsection
