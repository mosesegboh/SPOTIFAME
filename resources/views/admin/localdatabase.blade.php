
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAssistant())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Local Database</u></h2>
            <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">
                
              <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
              <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
                <input type="hidden" class="form-control" name="orderby" id="orderby" value="">


            <div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Filter Results:</h4>
                        

                <div class="form-group d-flex">
                  <input name="title" id="title" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name of Artist or Playlist..." value="{{ urldecode(request()->input('title')) }}" autocomplete="on">
                          
                 </div>

                        <div class="form-group row">
                            <label for="searchtype" class="lineheight24rem">Type:</label>
                            <div class="col-sm-9">
                                <select class="form-control border-secondary" id="searchtype" name="searchtype" autocomplete="off">
                                    <option value="artist"{{ urldecode(request()->input('searchtype'))=='artist' || urldecode(request()->input('searchtype'))=='' ? ' selected="selected"' :''}}>artist</option>
                                    <option value="playlist"{{ urldecode(request()->input('searchtype'))=='playlist' ? ' selected="selected"' :''}}>playlist</option>
                                </select>
                            </div>
                         </div>

                         <div id="genreswrap" class="form-group row">
                          <label for="genres" class="lineheight24rem">Genres:</label>
                            <div class="col-sm-9" data-toggle="tooltip" data-placement="auto" title="" data-original-title="You can search for tags such as house then it will list everything which start with house. If you want to be more specific use quotes: &quot;house&quot; then it will only list result with the specific word.">
                              <div class="card-body pl-2 pt-1 pb-1 pr-2">
                                <input name="genres" id="genres" value="{{ urldecode(request()->input('artistswithoutgenres'))=='on' ? '' : urldecode(request()->input('genres')) }}" autocomplete="off">
                              </div>
                            </div>
                        </div>

                        <div id="artistswithoutgenreswrap"{!! $searchresults[0]->type=='artist' ? '' : ' style="display: none;"' !!}>
                          <div class="form-check d-inline-block">
                            <label class="form-check-label">
                              <input name="artistswithoutgenres" id="artistswithoutgenres" type="checkbox" class="form-check-input"{!! urldecode(request()->input('artistswithoutgenres'))=='on' ? ' checked="checked"' :''!!} autocomplete="off">
                             Only Artist Without Genres
                            <i class="input-helper"></i></label>

                            <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="If you search with this: It will show those artist which does not have any tags."></i>

                        </div>
                      </div>


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

                        <div id="hidespotifyownedwrap"{!! $searchresults[0]->type=='playlist' ? '' : ' style="display: none;"' !!}>
                          <div class="form-check d-inline-block">
                            <label class="form-check-label">
                              <input name="hidespotifyowned" id="hidespotifyowned" type="checkbox" class="form-check-input"{!! urldecode(request()->input('hidespotifyowned'))=='on' ? ' checked="checked"' :''!!} autocomplete="off">
                             Hide Spotify Owned Playlists
                            <i class="input-helper"></i></label>

                            <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="If you search with this: It will hide spotify owned playlists."></i>

                        </div>
                      </div>



                      <div id="artistclaimwrap" class="position-relative p-4 border border-secondary"{!! $searchresults[0]->type=='artist' ? '' : ' style="display: none;"' !!}>

                        <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Filter Artists By Claimed State</span>
                        
                        
                                <div>
                                <div class="form-check d-inline-block">
                                <label class="form-check-label successful-bg">
                                <input name="claimedshow" id="claimedshow" type="checkbox" class="form-check-input"{!! ($claimedshow=='on') ? ' checked="checked"' :''!!} autocomplete="off">
                                              Claimed
                                      <i class="input-helper"></i></label>

                                      <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists which are checked by our script and the result is that they are claimed."></i>
                                </div>
                        
                            </div>
                        
                                <div>
                                <div class="form-check d-inline-block">
                                    <label class="form-check-label wesetitclaimed-bg">
                                    <input name="claimedshow2" id="claimedshow2" type="checkbox" class="form-check-input"{!! ($claimedshow2=='on') ? ' checked="checked"' :''!!} autocomplete="off">
                                               Claimed (changed)
                                          <i class="input-helper"></i></label>
                                          <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists which are set &quot;claimed&quot; by us."></i>
                                    </div>
                        
                                </div>
                        
                                <div>
                                    <div class="form-check d-inline-block">
                                        <label class="form-check-label problem-bg">
                                        <input name="notclaimedshow" id="notclaimedshow" type="checkbox" class="form-check-input"{!! ($notclaimedshow=='on') ? ' checked="checked"' :''!!} autocomplete="off">
                                                      Not claimed
                                              <i class="input-helper"></i></label>

                                              <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists which are checked by our script and the result is that they are not claimed"></i>
                                        </div>
                                    
                                    </div>
                        
                        
                                    <div>
                                    <div class="form-check d-inline-block">
                                            <label class="form-check-label waiting-bg">
                                            <input name="unknownshow" id="unknownshow" type="checkbox" class="form-check-input"{!! ($unknownshow=='on') ? ' checked="checked"' :''!!} autocomplete="off">
                                                       Unknown
                                                  <i class="input-helper"></i></label>

                                                  <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists which haven't been checked by our script and thus we don't know whether they are claimed or not."></i>
                                            </div>
                        
                                        </div>
                        
                            </div>


                        <button type="submit" class="btn btn-primary mt-2 float-right">Search</button>

                    </div>
                </div>
            </div>
            <!-- Normal search-->

            </form>
            

                        </div>
                    

                        <div id="searchresults" class="col-lg-12 px-0">
                            <div class="col-12 mb-5 mt-5 position-relative">
                                @if(!empty($searchresults))
                                <div class="form-group row position-absolute width200 right10 bottomminus42 mb-0">
                                        <label for="orderbychange" class="pt-2 pr-2">Order By:</label>
                                        <div class="width100">
                                   <select class="form-control form-control-sm orderbychange" id="orderbychange" name="orderbychange" autocomplete="off">
                                   <option {!! urldecode(request()->input('orderby'))=='' ? 'selected="selected"' :'' !!} value="">Spotify</option>
                                        <option {!! urldecode(request()->input('orderby'))=='followers' ? 'selected="selected"' :'' !!} value="followers">Followers</option>
                                        <option {!! urldecode(request()->input('orderby'))=='added' ? 'selected="selected"' :'' !!} value="added">Added</option>
                                        <option {!! urldecode(request()->input('orderby'))=='name' ? 'selected="selected"' :'' !!} value="name">Name</option>
                                      
                                    </select>
                                            </div>
                                </div>
                                @endif
                     <h2>Search Result For<u class="ml-2">"{{ urldecode(request()->input('title')) }}"</u></h2>
                         <p class="text-muted">{!! $paginationdisplay !!}</p>
                 
                            </div>
                 
                                     @if(!empty($searchresults))
                           <div class="col-lg-12 grid-margin">
                                           <div class="card results">
                                             <div class="card-body">

                                     @if ($searchresults[0]->type=='artist')
                  <div class="float-right">
                    <a class="fontsize25 downloadartistresultset" href="#">
                      <i class="mdi mdi-download" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Download current resultset. (First 100.000 elements.)"></i>
                    </a>
                  </div>
                                     @endif

                                           <div class="table-responsive">

                  @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                          @if ($searchresults[0]->type=='artist')
                                         @if($claimprogress !='1' && $claimprogress !='2')
                                            <p class="d-inline clearfix refreshmultiple refreshmorethanoneclaimstate"><a href="#" class=""
                                              ><i class="mdi mdi-refresh"></i
                                              ><span>Refresh multiple artists</span></a></p>
                                        @endif
                          @endif

                                        @if($claimprogress =='1')
                                        <p class="text-danger">This resultset is waiting in queue to be processed. <a href="{{ config('myconfig.config.server_url')  }}admin/claimedinprogress?getcurrentid={{ $thecacheid }}" target="_blank">More details</a></p>
                                         @endif

                                         @if($claimprogress =='2') 
                                         <p class="text-warning">This resultset is being processed. {{ $item_queue_count>0 ? '('.(100-(floor(($item_left/$item_queue_count)*100))).'% done.)' : '' }} <a href="{{ config('myconfig.config.server_url')  }}admin/claimedinprogress?getcurrentid={{ $thecacheid }}" target="_blank">More details</a></p>
                                         @endif

                                         @if($claimprogress =='0')
                                         <p class="text-success">(This resultset has been processed before. <a href="{{ config('myconfig.config.server_url')  }}admin/claimedinprogress?getcurrentid={{ $thecacheid }}" target="_blank">More details</a>)</p>
                                         @endif
                                         
                 @endif

           <p class="d-inline clearfix addmultipletogroup addmultipletogroupclick" data-type="{{ $searchresults[0]->type }}"><a href="#" class=""
                  ><i class="mdi mdi-folder-plus"></i
                  ><span>Add multiple items to group</span></a></p>

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

                                                  <th class="text-center width100">Link</th>
                                               
                                                  <th class="text-center width100">Note</th>

                                                  @if ($searchresults[0]->type=='artist' || $searchresults[0]->type=='playlist')
                                                  <th class="text-center width120">Added to DB<br>(GMT)</th>
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

                                                   <th class="text-center width100">Color Code</th>
                 
                                                 </tr>
                                               </thead>
                                               <tbody>
                 
                                                 @if(!empty($searchresults))
                                             @foreach ($searchresults as $searchresult)
                                             
                                             <tr id="parent_{{ $searchresult->mydbid }}" data-itemid="{{ $searchresult->mydbid }}" data-claimed="{{ $searchresult->claimed }}" data-db-table="spotify_items" {!! $searchresult->type=='artist' ? ($searchresult->claimed=='1' ? 'class="successful-bg"' : ($searchresult->claimed=='2' ? 'class="problem-bg"' : ( $searchresult->claimed=='3' ? 'class="wesetitclaimed-bg"' : 'class="waiting-bg"'))) :'' !!}>
                                                
                                                <td class="text-center position-relative pt-4">
                                     <span class="position-absolute actionbuttonswrap">
                                    @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                                                  @if ($searchresult->type=='artist')
                                                  <span class="refreshsingleartist getsingleclaimstate" data-toggle="tooltip" data-placement="auto" title="" data-original-title="You can refresh artist claimed state."><i class="mdi mdi-refresh"></i></span>  
                                                  @endif
                                     @endif
                                     <span class="addtogroupbutton addsingletogroupclick" data-name="{{ $searchresult->name }}" data-type="{{ $searchresult->type }}" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Add To Group"><i class="mdi mdi-folder-plus"></i></span>
                                     </span>
                                                  @if ($searchresult->images[2]->url)
                                                     <img class="h-auto" src="{{ $searchresult->images[2]->url }}" />
                                                     @elseif($searchresult->images[0]->url)
                                                     <img class="h-auto" src="{{ $searchresult->images[0]->url }}" />
                                                     @endif
                                                     @if ($searchresult->album->images[2]->url)
                                                     <img class="h-auto" src="{{ $searchresult->album->images[2]->url }}" />
                                                     <br>
                                                     @endif
                                                     <p>
                                                      @if ($searchresult->type=='artist')
                                                       <a class="d-inline-block h4 fontsize14" href="{{ config('myconfig.config.server_url')  }}admin/artist/{{ $searchresult->mydbid }}" target="_blank">{{ $searchresult->name }}</a>
                                                       @else
                                                       <span>{{ $searchresult->name }}</span>
                                                       @endif
                                                      @if (auth()->user()->isAdmin())
                                                      <br><span>{{ $searchresult->mydbid }}</span>
                                                      @endif
                                                    </p>
                                                   </td>
                 

                                                   @if ($searchresult->type=='artist' || $searchresult->type=='playlist')
                                                   <td class="text-center">{{ number_format($searchresult->followers->total) }}</td>
                                                   @endif

                                                   @if ($searchresult->type=='artist')
                                                   <td class="text-center position-relative claimfield myeditableelement" data-db-row="claimed" data-type="select" data-pk="" data-value="{{ $searchresult->claimed }}">{{ $searchresult->claimed=='1' ? 'Claimed' : ($searchresult->claimed=='2' ? 'Not Claimed' : ( $searchresult->claimed=='3' ? 'Claimed (changed)' : 'Unknown')) }}</td>
                                                  @endif
                                                  
                                                  <td class="text-center"><a class="d-inline-block h3" href="{{ $searchresult->external_urls->spotify }}" target="_blank"><i class="mdi mdi-link"></i></a></td>
                 

                                                    <td class="text-center notefield myeditableelement" data-db-row="note" data-type="textarea" data-pk="" data-placeholder="Your notes here..." data-title="Enter notes">{!! $searchresult->note !!}</td>

                                                  @if ($searchresult->type=='artist' || $searchresult->type=='playlist')
                                                  <td class="text-center">{{ date('d/m/Y H:i', $searchresult->timestamp) }}</td>
                                                  @endif
                 
                                                  @if ($searchresult->type=='playlist')
                                                   @if ($searchresult->owner->external_urls->spotify !='')
                                                   <td class="text-center"><a href="{{ $searchresult->owner->external_urls->spotify }}" target="_blank">{{ $searchresult->owner->display_name ? $searchresult->owner->display_name : 'Unkown' }}</a></td>
                                                 @else
                                                 <td class="text-center">{{ $searchresult->owner->display_name ? $searchresult->owner->display_name : 'Unkown' }}</td>
                                                 @endif
                                                 @endif
                 
                                                 @if ($searchresult->type=='artist')
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


                                                  <td class="text-center pickerfield" data-db-row="colorcode" data-value="{{ $searchresult->colorcode ? $searchresult->colorcode : '#FFF' }}">

                                                    <span class="color-picker"></span>
                                                  
                                                  </td>
                 
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
var csrf_token='{{ csrf_token() }}';

var search_string='{{$search_string}}';

var search_string_more='{{$search_string_more}}'

var searchset='{{request()->input('searchset')}}';
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
