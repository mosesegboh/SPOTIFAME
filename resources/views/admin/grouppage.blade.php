
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-lg-12">
                                  <div class="border-bottom text-center pb-4">
                                    <div class="mb-3">
                                      <h3>{{ $thegroup->name }}</h3>
                                    </div>
                                  
                                    <p class="w-75 mx-auto mb-3">Type: <b class="text-success">{{ ucfirst($thegroup->type) }} Group</b></p>
                                  </div>
                                  <div class="py-4">
                                    <p class="clearfix">
                                        <span class="float-left">
                                          Added
                                        </span>
                                        <span class="float-right text-muted">
                                            {{ date('d/m/Y H:i', $thegroup->timestamp) }}
                                        </span>
                                     </p>
                                     
                                    <p class="clearfix">
                                      <span class="float-left">
                                        Item Count
                                      </span>
                                      <span class="float-right text-muted">
                                        {{ $thegroup->item_count }}
                                      </span>
                                    </p>
                                  </div>

                                 <div class="py-2 border-top border-secondary">
                                    <p class="mb-0">
                                        Description:
                                    </p>
                                  <p>
                                    {{ $thegroup->description }}
                                  </p>
                                </div>
                                  @if($thegroup->note)
                                  <div class="py-2 border-top border-secondary">
                                    <p class="mb-0">
                                        Note:
                                    </p>
                                  <p>
                                    {{ $thegroup->note }}
                                  </p>
                                </div>
                                  @endif

                                  @if(!empty($thegroup->searchstrings))
                                  <div class="py-2 border-top border-secondary">
                                      <h5>Added resultsets:</h5>
                                    @foreach($thegroup->searchstrings as $index => $searchstring)
                                    <div class="pl-3">
                                        <p>{{ $index+1 }}.) resultset:</p>
                                        <a class="d-inline-block h4 fontsize14" href="{{ config('myconfig.config.server_url')  }}admin/localdatabase/{{ $searchstring->url }}" target="_blank">{{ $searchstring->searchstring }}</a><br>
                                    </div>
                                    @endforeach

                                  </div>
                                  @endif

                                
                                  
                                </div>

                              </div>

                    
                        </div>

                        <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">
                
                            <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
                            <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
                              <input type="hidden" class="form-control" name="orderby" id="orderby" value="">
                        </form>
                        
                    <div id="searchresults" class="col-lg-12 px-0" data-groupid="{{ $thegroup->id }}">
                            <div class="col-12 {!! ($thegroup->type=='artist' || $thegroup->type=='playlist') ? "mb-5" :"mb-2" !!} mt-2 position-relative">
                               @if(!empty($searchresults) && $thegroup->type=='artist' || $thegroup->type=='playlist')
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
                         <p class="text-muted">{!! $paginationdisplay !!}</p>
                 
                            </div>
                 
                                     @if(!empty($searchresults))
                           
@if ($thegroup->type=='artist' || $thegroup->type=='playlist')

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
                                     <span class="position-absolute actionbuttonswrap w-100">
                                    @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                                                  @if ($searchresult->type=='artist')
                                                  <span class="refreshsingleartist getsingleclaimstate" data-toggle="tooltip" data-placement="auto" title="" data-original-title="You can refresh artist claimed state."><i class="mdi mdi-refresh"></i></span>  
                                                  @endif
                                     @endif
                                     <span class="addtogroupbutton addsingletogroupclick" data-name="{{ $searchresult->name }}" data-type="{{ $searchresult->type }}" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Add To Group"><i class="mdi mdi-folder-plus"></i></span>
                                    
                                     <span class="quickremovefromgroupbutton quickremoveitemfromgroupclick" data-name="{{ $searchresult->name }}" data-type="{{ $thegroup->type }}" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Remove From Group"><i class="mdi mdi-folder-remove"></i></span>
                               
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


@if ($thegroup->type=='track')
                                    <div class="col-lg-12 grid-margin">
                                        <div class="card results">
                                        <div class="card-body">

                                        <div class="table-responsive">

                                        <table class="table table-hover resulttable">
                                            <thead>
                                            <tr>

                                            <th class="text-center width100">Track Name</th>

                                            <th class="text-center width100">Arist</th>

                                            <th class="text-center width100">Album</th>

                                            <th class="text-center width100">Playlist(s)</th>

                                            <th class="text-center width100">Popularity</th>

                                            <th class="text-center width100">Duration</th>

                                            <th class="text-center width100">BPM</th>

                                            <th class="text-center width100">Danceability</th>

                                            <th class="text-center width100">Key</th>

                                        
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @php ($tracks=$searchresults)
                                            @if(!empty($tracks))
                                        @foreach ($tracks as $track)
                                        
                                        <tr id="parent_{{ $track->id }}" data-itemid="{{ $track->id }}" data-realitemid="{{ $track->realitemid }}" data-db-table="spotify_tracks">
                                            
                                        
                                    <td class="text-center position-relative pt-4">
                                <span class="position-absolute actionbuttonswrap w100">
                                    <span class="addtogroupbutton addsingletogroupclick" data-name="{{ $track->name }}" data-type="track" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Add To Group"><i class="mdi mdi-folder-plus"></i></span>
                                    <span class="quickremovefromgroupbutton quickremoveitemfromgroupclick" data-name="{{ $track->name }}" data-type="{{ $thegroup->type }}" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Remove From Group"><i class="mdi mdi-folder-remove"></i></span>
                                </span> 

                                    <p>{{ $track->name }}</p>
                                    </td>


                                    <td class="text-center">
                                    <a href="https://open.spotify.com/artist/{{ $track->artistid }}" target="_blank">{{ $track->artistname }}</a>
                                    </td>

                                    <td class="text-center">
                                    <a href="https://open.spotify.com/album/{{ $track->albumid }}" target="_blank">{{  $track->albumname }}</a>
                                    </td>


                                    <td class="text-center">
                                    @if(!empty($track->playlists))
                                    @foreach ($track->playlists as $playlist)
                                    <a target="_blank" href="{{ config('myconfig.config.server_url')  }}admin/spotifyplaylists?searchset=1&pagenum=1&orderby=&accounttype=all&playlistid={{$playlist->spid}}">
                                    {{  $playlist->name }}</a><br>
                                    @endforeach
                                    @endif
                                    </td>

                                    <td class="text-center">
                                    {{ $track->popularity }}
                                    </td>


                                    <td class="text-center">
                                    {{ \App\Helpers\AppHelper::instance()->formatTime($track->info_duration_ms,1) }}
                                    </td>

                                    <td class="text-center">
                                    {{ $track->info_tempo }}
                                    </td>

                                    <td class="text-center">
                                    {{ $track->info_danceability }}
                                    </td>

                                    <td class="text-center">
                                    {{ $track->info_key }}
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
var searchset='{{request()->input('searchset')}}'
var item_count='{{$item_count}}';

var isadmin='{{auth()->user()->isAdmin()}}';


</script>

@endif
@endif
@endsection
