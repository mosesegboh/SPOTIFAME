
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Track Information Widget</u></h2>
            <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">
                
                <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
                <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
                  <input type="hidden" class="form-control" name="orderby" id="orderby" value="">


                  <div>
                    <div class="card">
                    <div class="card-body">
                            <h4 class="mb-3">Get Information about track:</h4>
                            
                        


                    <div class="form-group d-flex">
                      <input name="track" id="track" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Track Id..." value="{{ urldecode(request()->input('track')) }}" autocomplete="on">
                              
                     </div>

                            <button type="submit" class="btn btn-primary mt-2 float-right">Go</button>
    

                            <div>
                                
                                {!! $trackcontent !!}

       @if($thirdtrackcontent !='null')                         
 <br><p>Audio Analysis:</p><div style="max-height:500px;overflow-y:scroll;"><pre id="thirdtrackcontent"></pre></div></br>
        @endif                
                            </div>

                        </div>
                    </div>
                </div>
                  





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

                                                   <th class="text-center width100">Name</th>

                                                   <th class="text-center width100">Content</th>

                                                   <th class="text-center width100">Artist</th>

                                                   <th class="text-center width100">Album</th>

                                              
                                                 </tr>
                                               </thead>
                                               <tbody>
                 
                                                 @if(!empty($tracks))
                                             @foreach ($tracks as $track)
                                             
                                             <tr id="parent_{{ $track->id }}" data-itemid="{{ $track->id }}" data-db-table="spotify_tracks_infos">
                                                
                                              
                         <td class="text-center">{{  $track->name }}</td>
                         <td class="text-center showtrackcontent"><a href="#" target="_blank">Show Content</a></td>
                         <td class="text-center"><a href="{{ $track->artisturl }}" target="_blank">{{  $track->artistname }}</a></td>
                         <td class="text-center"><a href="{{ $track->albumurl }}" target="_blank">{{  $track->albumname }}</td>
                            <div class="trackinfocontent">
                            </div>
                        
                         
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

var thirdtrackcontent='{!! $thirdtrackcontent !!}';


</script>

@endif
@endif
@endsection
