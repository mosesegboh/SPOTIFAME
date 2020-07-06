
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())



<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Connected Accounts</u></h2>
           

            @if(count($notconnectedaccounts)>0)

            @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager() || auth()->user()->isPlaylister())
            <div id="addaccountwrap" class="position-relative p-4 border border-secondary">

              <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Not connected accounts</span>

              <!--
                <div>
                  <p>1.) Login to Spotify with your account:</p>
                  <a class="btn btn-primary btn-sm" href="https://artists.spotify.com/" target="_blank">
                      <i class="mdi mdi-spotify align-middle d-inline-flex mr-1"></i><span>Login</span>
                  </a>
                  </div>
               
                
                  <div class="mt-2">
             
                      <p>2.) Click on this button and grant the website access:</p>
      
                    <a class="btn btn-primary btn-sm" href="{{ config('myconfig.config.server_url')  }}admin/addspotifyaccount">
                      <i class="mdi mdi mdi-lock-open align-middle d-inline-flex mr-1"></i><span>Add or Refresh Account</span>
                  </a>
                 


                  @if(urldecode(request()->input('msg'))=='alreadyaddedbutupdating')
                  <p class="text-success">Thank you it was successful. Data refreshed.</p>
                  @endif
      
                  @if(urldecode(request()->input('msg'))=='successfullyadded')
                  <p class="text-success">Thank you it was successful.</p>
                  @endif
      
                  </div>
                -->

                  
                <div class="mt-4">
                  <p>These accounts are not connected to the spotify API, (user, or spotify revoked them), reconnect them if you want to use API functionalities:</p>
                  <ul>
                    @foreach ($notconnectedaccounts as $singleresult)
                    <li>{{ $singleresult->displayname}}</li>
                    @endforeach
                  </ul>
                </div>
               
             </div>
             @endif
             @endif

             


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
                    <input name="managersearch" id="managersearch" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name or URL or ID of the manager..." value="{{ urldecode(request()->input('managersearch')) }}" autocomplete="on">
                </div>
                                
                                
                  <div class="form-group d-flex advancedfield">
                      <input name="artistsearch" id="artistsearch" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name or URL or ID of the artist..." value="{{ urldecode(request()->input('artistsearch')) }}" autocomplete="on">
                  </div>

             @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAssistant())
                      <div class="form-group row">
                                <label for="accounttype" class="lineheight24rem">Type of Account:</label>
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

                        <div class="form-group row">
                                <label for="statetype" class="lineheight24rem">State of Account:</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary width120" id="statetype" name="statetype" autocomplete="off">
                                        <option value=""{{ urldecode(request()->input('statetype'))=='' ? ' selected="selected"' :''}}>- All -</option>
                                        <option value="1"{{ urldecode(request()->input('statetype'))=='1' ? ' selected="selected"' :''}}>Working</option>
                                        <option value="0"{{ urldecode(request()->input('statetype'))=='0' ? ' selected="selected"' :''}}>Processing</option>
                                        <option value="10"{{ urldecode(request()->input('statetype'))=='10' ? ' selected="selected"' :''}}>Problematic</option>
                                    </select>
                                </div>
                        </div>


                        <div class="form-group row">
                          <label for="country" class="lineheight24rem">Country:</label>
                          <div class="col-sm-9">
                              <select class="form-control border-secondary width120" id="country" name="country" autocomplete="off">
                                  <option value=""{{ urldecode(request()->input('country'))=='' ? ' selected="selected"' :''}}>- All -</option>
                                  @foreach ($countries as $countries_s)
                              <option value="{{$countries_s->country}}"{{ urldecode(request()->input('country'))==$countries_s->country ? ' selected="selected"' :''}}>{{$countries_s->country}} ({{$countries_s->account_count}})</option>
                                 @endforeach
                              </select>
                          </div>
                  </div>

                               <button type="submit" class="btn btn-primary mt-2 float-right">Filter</button>
                              </form>
                        </div>
                      </div>
                      
              
             </div>

                       
                        <div id="accounts" class="col-lg-12 grid-margin">
                                        <div class="card results">
                                          <div class="card-body">
              
                          <a class="btn btn-primary btn-sm addstraight" href="#">
                                              <i class="mdi mdi mdi-lock-open align-middle d-inline-flex mr-1"></i><span>Add Your Account</span>
                        </a>

                                        <div class="table-responsive">
                                          <table class="table table-hover resulttable">
                                            <thead>
                                              <tr>
                                                @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                                                <th class="text-center width100">Local Owner</th>
                                                 @endif
                                                <th class="text-center width100">Owner (Spotify)</th>
                                                
                      @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())
                                                <th class="text-center width100">Artist's Pick</th>
                     @endif

                       @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())
                                              <th class="text-center width100">Spotify Password</th>
                       @endif

                                                
                                                
                    @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())
                                                <th class="text-center width100">Artist</th>
                        @endif
                        @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())
                                                <th class="text-center width100">Connect Artist</th>
                                                @endif

                      @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isPlaylister())
                                          <th class="text-center width100">Playlists</th>
                      @endif
                                    <th class="text-center width100">Notes</th>

                  <th class="text-center width100">Country</th>

              
                       <th class="text-center width100">Email</th>
                                                
              
                                              </tr>
                                            </thead>
                                            <tbody>
              
                                              @if(!empty($allresults))
                                          @foreach ($allresults as $singleresult)
                                            <tr id="parent_{{ $singleresult->id }}" data-itemid="{{ $singleresult->id }}" data-db-table="spotify_accounts_auth" data-artistid="{{ $singleresult->artistid }}" data-realartistid="{{ $singleresult->realartistid }}">

                                  @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                                  <td class="text-center position-relative pt-4">
 
    <span class="position-absolute actionbuttonswrap">
      @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
         <span class="removebutton removemanagerclick" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Remove the Manager Account"><i class="mdi mdi-delete"></i></span>
       @endif

       </span>

                     
                                    <a target="_blank" href="{{ config('myconfig.config.server_url')  }}admin/users?searchset=1&pagenum=1&username={{ $singleresult->username }}&advancedsearch=1"
                                    >{{ $singleresult->username }}</a>
                                  
                                  </td>
                                    @endif


                                                <td class="text-center">

                                                  @if ($singleresult->state=='1')
                                                      @if ($singleresult->image)
                                                    <img class="h-auto" src="{{ $singleresult->images }}" />
                                                    @endif
                                                    <p class="mb-0">
                                                      <a class="d-inline-block h4 mb-0 fontsize14" href="{{ $singleresult->url }}" target="_blank">{{ $singleresult->displayname }}</a>
                                                    </p>

                                                    @else
                                                      @if ($singleresult->state=='10')
                                                    <span class="text-danger">(Problematic)</span>
                                                    @elseif($singleresult->state=='0')
                                                    <span class="text-warning">(Processing...)</span>
                                                      @endif
                                                    @endif

                                                </td>

                            @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())
                                 <td class="text-center"><a class="changeartistpick" title="{{$singleresult->artistpickname}}" href="#" data-exist="{{$singleresult->artistpick ? '1' : '0'}}" data-artistpick="{{ $singleresult->artistpick }}">{!! $singleresult->artistpick ?  ($singleresult->artistpickimage ? '<img class="artistpickpic" src="'.$singleresult->artistpickimage.'" />' :'Change Artist\'s Pick') : 'Add Artist\'s Pick'!!}{!! $singleresult->artistpickstate=='1' ? '<br><span class="text-warning">(Change in progress...)</span>' : ($singleresult->artistpickstate=='2' ? '<br><span class="text-warning">(Getting from Spotify...)</span>' : ($singleresult->artistpickstate=='10' ? '<br><span class="text-danger">(Problematic!)</span>' : (($singleresult->artistpick!='' && $singleresult->state=='1' && $singleresult->active=='1') ? '<br><span class="text-success">(Success!)</span>' : ''))) !!}</a></td>
                             @endif
                             
    
                                  @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())
                                                    <td class="text-center"><a class="changeartistpassword" href="#" data-exist="{{$singleresult->thingstr ? '1' : '0'}}" data-thingstr="{{$singleresult->thingstr}}">{{ $singleresult->thingstr ? 'Change' : 'Add'}} Spotify Password</a></td>
                                    @endif


                                                

                                                @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())
                                                <td class="text-center theartistnamewrap">
                                                  @if (in_array('artistmanager',$singleresult->roles) || auth()->user()->isAdmin() || auth()->user()->isEditor())
                                                  @if ($singleresult->artistname)
                                                  <a class="d-inline-block h4 mb-0 fontsize14" href="{{ config('myconfig.config.server_url')  }}admin/artist/{{ $singleresult->mydbid }}" target="_blank">{{ $singleresult->artistname }}</a>
                                                  <span class="removebutton removeartistclick" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Remove the Artist"><i class="mdi mdi-delete"></i></span>
                                                  <div class="mt-2" data-toggle="tooltip" data-placement="auto" title="" data-original-title="You can turn on/off artist, if turned off, artistpick won't be changed, also artist tokens won't be checked.">
                                                    <input class="turnonoffartist" type="checkbox" {!! $singleresult->artistactive==1 ? 'checked="checked"' : '' !!} data-toggle="toggle" data-size="sm" autocomplete="off" />                  
                                                    </div>
                                                    @endif
                                                 @else
                                                  <div class="d-flex justify-content-center"><i class="mdi mdi-close-octagon text-danger fontsize20" data-toggle="tooltip" data-placement="auto" title="" data-original-title="This user is not an artistmanager so he/she does not have any artist accounts."></i></div>
                                                  @endif
                                                </td>
                                                @endif

                                              @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())
                                              <td class="text-center"><a class="connectartist" href="#" data-generatedlink="{{ $singleresult->generatedstr }}">{{$singleresult->artistconnectid ?  'Change Artist' : 'Add Artist'}}</a></td>

                                              <!--<td class="text-center"><a class="connectartist" href="#" data-generatedlink="{{ $singleresult->generatedstr }}">{{$singleresult->artistconnectid ? (($singleresult->istokensfine) ? 'Refresh Token' : 'Add Token') : 'Add Artist'}}</a></td>-->
                                                @endif

                               @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isPlaylister())
                                              <td class="text-center"> 
                                                @if (in_array('playlister',$singleresult->roles) || auth()->user()->isAdmin() || auth()->user()->isEditor())
                                                  @if ($singleresult->playlistcount==0 || empty($singleresult->playlists))
                                                  0
                                                  @else
                                                    @foreach ($singleresult->playlists as $pl_index => $playlist)
                                                    <a href="{{ $playlist->playlisturl }}" target="_blank">{{ $pl_index!=0 ? ', ' :''}}{{ $playlist->playlistname }}</a>

                                                    @endforeach
                                                    @if(count($singleresult->playlists)>5)
                                                    <br>...<br>
                                                    <a href="{{ config('myconfig.config.server_url')  }}admin/spotifyplaylists" target="_blank">More Playlists</a>
                                                    @endif
                                                  @endif
                                                
                                                @else
                                                <div class="d-flex justify-content-center"><i class="mdi mdi-close-octagon text-danger fontsize20" data-toggle="tooltip" data-placement="auto" title="" data-original-title="This user is not an playlister so he/she does not have any playlists."></i></div>
                                                @endif
                                              </td>
                                 @endif
                                                

                                                @if (auth()->user()->id==$singleresult->userid)
                                                <td class="text-center notefield myeditableelement" data-db-row="note" data-type="textarea" data-pk="" data-placeholder="Your notes here..." data-title="Enter notes">{!! $singleresult->note !!}</td>
                                                @else
                                                <td class="text-center">{!! $singleresult->note !!}</td>
                                                @endif

                                                <td class="text-center">{{ $singleresult->country }}</td>
                                                <td class="text-center">{{ $singleresult->email }}</td>


                                              </tr>
                                              
              
                                              @endforeach
                                          @endif
              
                                            </tbody>
                                          </table>
                                        </div>
              
                                      </div>
                                    </div>
                                  
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

//var accessToken = "";



</script>

@endif
@endsection
