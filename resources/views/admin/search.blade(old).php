
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())



<div class="main-panel">
<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-12">
                            <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get">

                                <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">

                                <div class="form-group d-flex">
                              <input style="height:43px;padding-right:10px;padding-left:10px;" name="title" id="title" type="text" class="form-control border-secondary" placeholder="Type in something *" value="{{ urldecode(request()->input('title')) }}">
                              <div class="input-group-btn">
                                <div class="btn-group" role="group">
                              <button id="openadvsearch"{{ urldecode(request()->input('advopen'))=='1' ? ' data-clicks="1"' :''}} type="button" style="padding-right:10px;padding-left:10px;" class="btn btn-outline-success" data-toggle="collapse" href="#collapse-8" aria-expanded="false" aria-controls="collapse-8">
                               <i class="mdi mdi-filter"></i>
                                  </button>  
                              <button type="submit" class="btn btn-primary ml-0">Search</button>
                            </div>
                                </div>
                     </div>

                     @if ($responsestatus)
                        @if($responsestatus=='429')
                        <div class="alert alert-danger">Information: This was a hard search with a lot of results to sort, that is why it took so long! </div>
                        @elseif($responsestatus!='200' && auth()->user()->isAdmin())
                        <div class="alert alert-danger">Message to developer: response status: {{$responsestatus}}</div>
                        @endif      
                    @endif  
<!-- Advanced search -->


    <div id="collapse-8" class="collapse{{ urldecode(request()->input('advopen'))=='1' ? ' show' :''}}" role="tabpanel" aria-labelledby="heading-8"  style="">
        <div class="card">
        <div class="card-body">
          
            <h4 class="mb-3">Advanced Search:</h4>


    <input type="hidden" class="" id="advopen" name="advopen" value="{{ urldecode(request()->input('advopen')) }}">


    <div class="spotifysearchpart" style="padding: 32px;border: 1px solid #ccc;position:relative;"> <!-- spotify search part -->

      <span style="color: #fff;position: absolute;top: -10px;background: #131633;padding-right: 10px;padding-left: 10px;">Spotify Api Filters</span>

    <div class="form-group row">
      <label for="searchtype" class="" style="line-height: 2.4rem;">Type:</label>
      <div class="col-sm-9">
          <select class="form-control border-secondary" id="searchtype" name="searchtype">
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
    <label for="searchtype" class="" style="line-height: 2.4rem;">Genres:</label>
      <div class="col-sm-9">
        <div class="card-body" style="padding: 0.45rem 0.75rem !important;">
          <input name="genres" id="genres" value="{{ urldecode(request()->input('genres')) }}" />
        </div>
      </div>
  </div>

  
          @if(!empty($markets))
          <div class="form-group row">
                <label for="marketselect" class="" style="line-height: 2.4rem;">Market:</label>
                <div class="col-sm-9">
                    <select class="form-control border-secondary" id="marketselect" name="marketselect">
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
                <label for="artist" class="" style="line-height: 2.4rem;">Artist</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control border-secondary" id="artist" name="artist" placeholder="" value="{{ urldecode(request()->input('artist')) }}">
                </div>
              </div>

              <div class="form-group row">
                <label for="track" class="" style="line-height: 2.4rem;">Track</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control border-secondary" id="track" name="track" placeholder="" value="{{ urldecode(request()->input('track')) }}">
                </div>
              </div>

              <div class="form-group row">
                <label for="album" class="" style="line-height: 2.4rem;">Album</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control border-secondary" id="album" name="album" placeholder="" value="{{ urldecode(request()->input('album')) }}">
                </div>
              </div>

-->

  <div id="yearfromtowrap" class="form-group row">

    <div class="input-group input-daterange">
      <label class="col-6 col-lg-4 col-form-label" style="line-height: 0.4rem;max-width: 136px;">Between Years:</label>
      <input type="text" id="yearfrom" name="yearfrom" class="form-control" value="{{ urldecode(request()->input('yearfrom')) ? urldecode(request()->input('yearfrom')) :'1900' }}">
      <div class="input-group-text" style="height:35px;">-</div>
      <input type="text" id="yearto" name="yearto" class="form-control" value="{{ urldecode(request()->input('yearto')) ? urldecode(request()->input('yearto')) : now()->year }}">
  </div>



  </div>



             <div id="isnewwrap" class="form-check">
                               <label class="form-check-label">
                                 <input name="isnew" id="isnew" type="checkbox" class="form-check-input"{{ urldecode(request()->input('isnew'))=='on' ? ' checked="checked"' :''}}>
                                Is new
                               <i class="input-helper"></i></label>
             </div>


        
        </div> <!-- spotify search part -->


        <div class="oursearchpart" style="padding: 32px;border: 1px solid #ccc;position:relative;margin-top:20px;"> <!-- our search part -->
            
          <span style="color: #fff;position: absolute;top: -10px;background: #131633;padding-right: 10px;padding-left: 10px;">Our Filters</span>

             <div id="followerwrap" class="slider-wrap mt-4 mb-4">
                <p class="">Followers:</p>
                <input type="text" id="followers" name="followers" value="" />


     <div class="input-group">
                  <label class="col-6 col-lg-4 col-form-label" style="line-height: 0.4rem;max-width: 136px;">Type in:</label>
                  <input type="text" id="fromfollowers" class="form-control" value="{{ urldecode(request()->input('followers')) ? \App\Helpers\AppHelper::instance()->transformRangeValue(explode(';',urldecode(request()->input('followers')))[0],$rangeslidervalues[2],$rangeslidervalues[1]) : $rangeslidervalues[0] }}" autocomplete="off">
                  <div class="input-group-text" style="height:35px;">-</div>
                  <input type="text" id="tofollowers" class="form-control" value="{{ urldecode(request()->input('followers')) ? \App\Helpers\AppHelper::instance()->transformRangeValue(explode(';',urldecode(request()->input('followers')))[1],$rangeslidervalues[2],$rangeslidervalues[1]) : $rangeslidervalues[1] }}" autocomplete="off">
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

      <div id="searchresults">
           <div class="col-12 mb-5 mt-5">
                            <h2>Search Result For<u class="ml-2">"{{ urldecode(request()->input('title')) }}"</u></h2>
                            <p class="text-muted">{!! $paginationdisplay !!}</p>
                          </div>
                          <div class="col-12 results">

                            @if(!empty($searchresults))
                            @foreach ($searchresults as $searchresult)
                            
                            <div class="pt-4 pb-4 border-bottom" {!! $searchresult->type=='artist' ? $searchresult->claimed ? 'style="background: #345130;"' :'style="background: #642626;"' :'' !!}>
                                <div class="clearfix" style="padding-right: 15px;float:left;width:100px;height:auto;">
                                  @if ($searchresult->images[2]->url)
                                  <img style="width:100%;height:auto;" src="{{ $searchresult->images[2]->url }}" />
                                  @elseif($searchresult->images[0]->url)
                                  <img style="width:100%;height:auto;" src="{{ $searchresult->images[0]->url }}" />
                                  @endif
                                  @if ($searchresult->album->images[2]->url)
                                  <img style="width:100%;height:auto;" src="{{ $searchresult->album->images[2]->url }}" />
                                  @endif
                                </div>

                              <div class="clearfix" style="padding-left: 102px;">
                                <a class="d-inline-block h4" href="{{ $searchresult->external_urls->spotify }}">{{ $searchresult->name }} ({{ $searchresult->type }})</a>
                                
                                

                                 @if ($searchresult->type=='playlist')
                                 <p class="page-description mt-1 w-75">Public:
                                  <span style="font-size:20px;" class="{!! (!$searchresult->public || empty($searchresult->public)) ? 'text-success' :'text-danger' !!}" title="{!! (!$searchresult->public || empty($searchresult->public))  ? 'Public' :'Private' !!}">{!! (!$searchresult->public || empty($searchresult->public))  ? '<i class="fa fa-check-circle"></i>' :'<i class="fa fa-times-circle"></i>' !!}</p>
                                  @endif

                                 @if ($searchresult->duration)
                                 - <span>{{$searchresult->duration}}</span>
                                 @endif

                                 @if ($searchresult->album)
                                  @if ($searchresult->album->external_urls->spotify !='')
                                  <p class="page-description mt-1 w-75">Album: <a href="{{ $searchresult->album->external_urls->spotify }}" target="_blank">{{ $searchresult->album->name ? $searchresult->album->name : 'Unkown' }}</a></p>
                                  @else
                                  <p class="page-description mt-1 w-75">Album: {{ $searchresult->album->name ? $searchresult->album->name : 'Unkown' }}</p>
                                  @endif
                                 @endif


                                 @if ($searchresult->artists)
                                  @if ($searchresult->artists[0]->external_urls->spotify !='')
                                  <p class="page-description mt-1 w-75">{{ ucfirst($searchresult->artists[0]->type) }}: <a href="{{ $searchresult->artists[0]->external_urls->spotify }}" target="_blank">{{ $searchresult->artists[0]->name ? $searchresult->artists[0]->name : 'Unkown' }}</a></p>
                                  @else
                                  <p class="page-description mt-1 w-75">{{ ucfirst($searchresult->artists[0]->type) }}: {{ $searchresult->artists[0]->name ? $searchresult->artists[0]->name : 'Unkown' }}</p>
                                  @endif
                                 @endif

                                 @if ($searchresult->show)
                                 @if ($searchresult->show->external_urls->spotify !='')
                                 <p class="page-description mt-1 w-75">Show: <a href="{{ $searchresult->show->external_urls->spotify }}" target="_blank">{{ $searchresult->show->name ? $searchresult->show->name : 'Unkown' }}</a></p>
                                 @else
                                 <p class="page-description mt-1 w-75">Show: {{ $searchresult->show->name ? $searchresult->show->name : 'Unkown' }}</p>
                                 @endif
                                @endif
                                 



                                 @if ($searchresult->release_date)
                                 <p class="page-description mt-1 w-75">Release Date: {{ date('d/m/Y', strtotime($searchresult->release_date)) }}</p>
                                 @endif

                                 @if ($searchresult->album->release_date)
                                 <p class="page-description mt-1 w-75">Release Date: {{ date('d/m/Y', strtotime($searchresult->album->release_date)) }}</p>
                                 @endif

                                 @if ($searchresult->type=='album')
                                 @if ($searchresult->type)
                                 <p class="page-description mt-1 w-75">Type: {{$searchresult->type}}</p>
                                 @endif
                                 @endif

                                 @if ($searchresult->type=='show')
                                  @if ($searchresult->media_type)
                                  <p class="page-description mt-1 w-75">Type: {{$searchresult->media_type}}</p>
                                  @endif
                                 @endif

                                 @if ($searchresult->type=='show')
                                 @if ($searchresult->publisher)
                                  <p class="page-description mt-1 w-75">Publisher: {{$searchresult->publisher}}</p>
                                  @endif
                                 @endif
                                 


                                 @if ($searchresult->type=='artist' || $searchresult->type=='playlist')
                              <p class="d-block h5">Followers: {{ number_format($searchresult->followers->total) }}</p>
                                @endif

                                @if ($searchresult->type=='playlist')
                                  @if ($searchresult->owner->external_urls->spotify !='')
                                <p class="page-description mt-1 w-75">Owner: <a href="{{ $searchresult->owner->external_urls->spotify }}" target="_blank">{{ $searchresult->owner->display_name ? $searchresult->owner->display_name : 'Unkown' }}</a></p>
                                @else
                                <p class="page-description mt-1 w-75">Owner: {{ $searchresult->owner->display_name ? $searchresult->owner->display_name : 'Unkown' }}</p>
                                @endif
                                @endif

                              @if ($searchresult->claimed)
                                @if ($searchresult->distributorurl !='')
                                <p class="page-description mt-1 w-75">Distributor: <a href="{{ $searchresult->distributorurl }}" target="_blank">{{ $searchresult->distributor ? $searchresult->distributor : 'Unkown' }}</a></p>
                                @else
                                <p class="page-description mt-1 w-75">Distributor: {{ $searchresult->distributor ? $searchresult->distributor : 'Unkown' }}</p>
                                @endif
                              @endif

                              @if ($searchresult->genres)
                              <p class="page-description mt-1 w-75">Genres: {{ $searchresult->genres ? implode(', ', $searchresult->genres) :'-' }}</p>
                              @endif

                              <p class="page-description mt-1 w-75 text-muted"></p>

                              </div>
                            </div>
                            @endforeach
                            @endif
                          </div>

                          
       
                          <nav class="col-12" aria-label="Page navigation">
                            {!! $pagination !!}
                          </nav>

            
           </div>  
                        
                        

                        </div>
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
