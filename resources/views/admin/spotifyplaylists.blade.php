
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())



<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Connected Playlists</u></h2>
           
           
<!--
            @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isPlaylister())
            
            <div id="addaccountwrap" class="position-relative p-4 border border-secondary">

              <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Add Playlists</span>

                <div>
                  <p>1.) Login to Spotify with your playlist owner account:</p>
                  <a class="btn btn-primary btn-sm" href="https://www.spotify.com/" target="_blank">
                      <i class="mdi mdi-spotify align-middle d-inline-flex mr-1"></i><span>Login</span>
                  </a>
                  </div>
  
                  <div class="mt-2">
                      <p>2.) Click on this button to get spotify playlists:</p>
      
                    <a class="btn btn-primary btn-sm openplaylistadd" href="#">
                      <i class="mdi mdi-playlist-plus align-middle d-inline-flex mr-1"></i><span>Add or Refresh Playlists</span>
                  </a>
      
                  @if(urldecode(request()->input('msg'))=='noplaylists')
                  <p class="text-success">Thank you it was successful. But there were no playlists found.</p>
                  @endif
      
                  @if(urldecode(request()->input('msg'))=='successfullyadded')
                  <p class="text-success">Thank you it was successful.</p>
                  @endif
      
                  </div>
            

             </div>
             @endif
            -->

            <div>
        
           
            </div>


                        </div>

         <div class="col-lg-12 mb-4">  
                          
            
                          <div class="card">
                          <div class="card-body">

                            <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">
                            
                              <h4 class="mb-4">Filter results:</h4>
                             
                              <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
                              <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
                                <input type="hidden" class="form-control" name="orderby" id="orderby" value="">


     <div class="form-group d-flex advancedfield">
             <input name="managersearch" id="managersearch" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name or URL or ID of the owner..." value="{{ urldecode(request()->input('managersearch')) }}" autocomplete="on">
    </div>
                            
                 @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAssistant())
                          <div class="form-group row">
                                  <label for="accounttype" class="lineheight24rem">Type of Owner Account:</label>
                                  <div class="col-sm-9">
                                      <select class="form-control border-secondary width120" id="accounttype" name="accounttype" autocomplete="off">
                                          <option value=""{{ urldecode(request()->input('accounttype'))=='' ? ' selected="selected"' :''}}>- My Accounts -</option>
                                          @if (!auth()->user()->isAssistant())
                                          <option value="all"{{ urldecode(request()->input('accounttype'))=='all' ? ' selected="selected"' :''}}>All Accounts</option>
                                          <option value="generated"{{ urldecode(request()->input('accounttype'))=='generated' ? ' selected="selected"' :''}}>Generated Accounts</option>
                                          @endif
                                          <option value="assistant"{{ urldecode(request()->input('accounttype'))=='assistant' ? ' selected="selected"' :''}}>Assistant Created</option>
                                      </select>
                                  </div>
                          </div>
                 @endif             

                               <button type="submit" class="btn btn-primary mt-2 float-right">Filter</button>
                              </form>
                        </div>
                      </div>
                      
              
             </div>
                  
                             
                  
                  
                    
                       
                        <div id="playlists" class="col-lg-12 grid-margin">
                                        
              
                         @if(empty($allresults))

                         <div class="card results">
                            <div class="card-body">

                                 <div class="table-responsive"><p>No added playlists yet.</p></div>
                            </div>
                        </div>

                         @else
                           @foreach ($allresults as $singleresult)

                           <div class="card results">
                            <div class="card-body">

                                        <div class="table-responsive">
          <h4 class="border-bottom border-secondary pb-1">

            @if (auth()->user()->id==$singleresult->userid)
            <div class="d-inline-block">
                <a class="d-flex text-info align-bottom refreshsinglemanager" data-managerid="{{ $singleresult->managerid }}" data-name="{{ $singleresult->displayname }}" href="#"><i class="mdi mdi-refresh"></i></a>
            </div>  
            @endif

            <a target="_blank" href="{{ $singleresult->url }}">{{ $singleresult->displayname }}</a><span class=""> owns {{ $singleresult->playlist_count }} Playlists:</span>
        
            <div class="d-inline-block">
                <a class="d-flex text-info align-bottom myopencollapse collapsed" data-toggle="collapse" href="#collapse-{{ $singleresult->id }}" aria-expanded="false" aria-controls="collapse-{{ $singleresult->id }}"><i class="mdi myopenicon"></i></a>
            </div>  
        </h4>
        <div id="collapse-{{ $singleresult->id }}" class="collapse" role="tabpanel" aria-labelledby="heading-{{ $singleresult->id }}">
                                          <table class="table table-hover resulttable">
                                            <thead>
                                              <tr>

                                                <th class="text-center width100">Playlist Name</th>

                                                <th class="text-center width100">Followers</th>

                                                <th class="text-center width100">Link</th>

                                                <th class="text-center width100">Notes</th>

                                                @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                                                <th class="text-center width100">Local Owner</th>
                                                 @endif
                                                <th class="text-center width100">Owner (Spotify)</th>
                                                
                                                
                                                
              
                                              </tr>
                                            </thead>
                                            <tbody>
              
                                              @if(!empty($singleresult->playlists))
                                          @foreach ($singleresult->playlists as $singleplaylistresult)

                                            <tr id="parent_{{ $singleplaylistresult->id }}" data-playlistid="{{ $singleplaylistresult->playlistid }}" data-itemid="{{ $singleplaylistresult->mydbid }}" data-db-table="spotify_items" data-managerid="{{ $singleresult->managerid }}">


                                     <td class="text-center position-relative pt-4">
                                       <span class="position-absolute actionbuttonswrap">
                                   @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                                      <span class="removebutton removeplaylistclick" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Remove the Playlist"><i class="mdi mdi-delete"></i></span>
                                    @endif
                                    <span class="addtogroupbutton addsingletogroupclick" data-name="{{ $singleplaylistresult->name }}" data-type="playlist" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Add To Group"><i class="mdi mdi-folder-plus"></i></span>
                                    </span>
                                    
                                                    @if ($singleplaylistresult->imageurl)
                                                       <img class="h-auto" src="{{ $singleplaylistresult->imageurl }}" />
                                                     @endif
                                                       <p>
                                                         <span>{{ $singleplaylistresult->name }}</span>
                                                      </p>
                                     </td>

                        
                                 
                             <td class="text-center">{{ number_format($singleplaylistresult->followercount) }}</td>
                                  
                                        


                                    <td class="text-center"><a class="d-inline-block h3" href="{{ $singleplaylistresult->url }}" target="_blank"><i class="mdi mdi-link"></i></a></td>

                                                @if (auth()->user()->id==$singleplaylistresult->userid)
                                                <td class="text-center notefield myeditableelement" data-db-row="note" data-type="textarea" data-pk="" data-placeholder="Your notes here..." data-title="Enter notes">{!! $singleplaylistresult->note !!}</td>
                                                @else
                                                <td class="text-center">{!! $singleplaylistresult->note !!}</td>
                                                @endif


                                @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                                  <td class="text-center">
                                    <a target="_blank" href="{{ config('myconfig.config.server_url')  }}admin/users?searchset=1&pagenum=1&username={{ $singleresult->username }}&advancedsearch=1"
                                    >{{ $singleresult->username }}</a>
                                  
                                  </td>
                                    @endif

                                    @if ($singleplaylistresult->ownerurl !='')
                                                    <td class="text-center">
                                                        <a href="{{ $singleplaylistresult->ownerurl }}" target="_blank">{{ $singleplaylistresult->ownername ? $singleplaylistresult->ownername : 'Unkown' }}</a>
                                                    </td>
                                                    @else
                                                    <td class="text-center">{{ $singleplaylistresult->ownername ? $singleplaylistresult->ownername : 'Unkown' }}</td>
                                     @endif              



                                          
                                                

                                              </tr>
                                              
              
                                              @endforeach
                                          @endif
              
                                            </tbody>
                                          </table>

                                          <div class="text-right">
                                            <a class="text-danger" href="#collapse-{{ $singleresult->id}}" data-target="#collapse-{{ $singleresult->id}}" data-toggle="collapse">Close</a>
                                          </div>
                           </div>


                                        </div>

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
<!-- content-wrapper ends -->

<script>
var csrf_token='{{ csrf_token() }}';
var searchset='{{request()->input('searchset')}}'
var item_count='{{$item_count}}';

var isadmin='{{auth()->user()->isAdmin()}}';


</script>

@endif
@endsection
