
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Our Tracks</u></h2>
            <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">
                
                <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
                <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
                  <input type="hidden" class="form-control" name="orderby" id="orderby" value="">


     <div class="form-group d-flex advancedfield">
  <input name="namesearch" id="namesearch" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name or URL or ID of the track..." value="{{ urldecode(request()->input('namesearch')) }}" autocomplete="on">
       </div>


     <div class="form-group d-flex advancedfield">
              <input name="artistsearch" id="artistsearch" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name or URL or ID of the artist..." value="{{ urldecode(request()->input('artistsearch')) }}" autocomplete="on">
         </div>
               
         <div class="form-group d-flex advancedfield">
          <input name="albumsearch" id="albumsearch" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name or URL or ID of the album..." value="{{ urldecode(request()->input('albumsearch')) }}" autocomplete="on">
             </div>

   <div class="form-group d-flex advancedfield">
              <input name="playlistsearch" id="playlistsearch" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name or URL or ID of the playlist..." value="{{ urldecode(request()->input('playlistsearch')) }}" autocomplete="on">
                 </div>


             <button type="submit" class="btn btn-primary mt-2 float-right">Filter</button>

            </form>
            



                        </div>
                    

                        <div id="tracks" class="col-lg-12 px-0">
                            <div class="col-12 mb-2 mt-2 position-relative">
                                
                         <p class="text-muted">{!! $paginationdisplay !!}</p>
                 
                            </div>
                 
                                     @if(!empty($tracks))
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
                 
                                                 @if(!empty($tracks))
                                             @foreach ($tracks as $track)
                                             
                                             <tr id="parent_{{ $track->id }}" data-itemid="{{ $track->id }}" data-realitemid="{{ $track->realitemid }}" data-db-table="spotify_tracks">
                                                
                                              
                         <td class="text-center position-relative pt-4">
                <span class="position-absolute actionbuttonswrap">
                            <span class="addtogroupbutton addsingletogroupclick" data-name="{{ $track->name }}" data-type="track" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Add To Group"><i class="mdi mdi-folder-plus"></i></span>
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

var nowdate=moment().format('DD/MM/YYYY');
var nowdateyear=moment().format('YYYY');


</script>

@endif
@endif
@endsection
