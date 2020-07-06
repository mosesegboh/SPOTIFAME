
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Genres</u></h2>
          <p class="text-muted text-center">(Here you can see the genres in our database. The item counts represents the exact match search for those genres.)</p>
            
          <form id="currentform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">
                
            <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
            <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
              <input type="hidden" class="form-control" name="orderby" id="orderby" value="">

              <div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Filter Results:</h4>
                        

                <div class="form-group d-flex">
                  <input name="title" id="title" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name of Genre..." value="{{ urldecode(request()->input('title')) }}" autocomplete="on">
                          
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
                                @if(!empty($allresults))
                                <div class="form-group row position-absolute width200 right10 bottomminus42 mb-0">
                                        <label for="orderbychange" class="pt-2 pr-2">Order By:</label>
                                        <div class="width100">
                                   <select class="form-control form-control-sm orderbychange" id="orderbychange" name="orderbychange" autocomplete="off">
                                   <option {!! urldecode(request()->input('orderby'))=='' ? 'selected="selected"' :'' !!} value="">Connected Artists</option>
                                        <option {!! urldecode(request()->input('orderby'))=='added' ? 'selected="selected"' :'' !!} value="added">Added</option>
                                        <option {!! urldecode(request()->input('orderby'))=='name' ? 'selected="selected"' :'' !!} value="name">Name</option>
                                      
                                    </select>
                                            </div>
                                </div>
                                @endif
                     
                         <p class="text-muted">{!! $paginationdisplay !!}</p>
                 
                            </div>
                 
                                     @if(!empty($allresults))
                           <div class="col-lg-12 grid-margin">
                                           <div class="card results">
                                             <div class="card-body">

                                              <div class="">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="d-inline-block width200 pr-2 fontsize14">
                                                          <a href="{{ config('myconfig.config.server_url')  }}admin/localdatabase?searchset=1&pagenum=1&orderby=&title=&searchtype=artist&genres=&followers=0%3B100000000&claimedshow=on&claimedshow2=on&notclaimedshow=on&unknownshow=on&artistswithoutgenres=on" target="_blank"><span class="position-relative">Artists without genres<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists without any genres."></i></span></a>
                                                        </div>
                                                        <div class="d-inline-block width100">
                                                        <h6>{{ number_format($nogenreartists) }}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                           <div class="table-responsive">

                                             <table class="table table-hover resulttable">
                                               <thead>
                                                 <tr>
                                                   <th class="text-center width100">Name</th>
                 
                                                   <th class="text-center width120">Artist Connected</th>

                                                   <th class="text-center width120">Genre's Playlist</th>

                                                   <th class="text-center width120">Added</th>
                                                   
                                                 </tr>
                                               </thead>
                                               <tbody>
                 
                                                 @if(!empty($allresults))
                                             @foreach ($allresults as $allresult)
                                             
                                             <tr id="parent_{{ $allresult->id }}" data-itemid="{{ $allresult->id }}" data-db-table="spotify_genres">
                                                
                                              
                                             <td class="text-center"><a href="{{ config('myconfig.config.server_url')  }}admin/localdatabase?searchset=1&pagenum=1&orderby=&title=&searchtype=artist&genres=%22{{ $allresult->name }}%22&followers=0%3B100000000&claimedshow=on&claimedshow2=on&notclaimedshow=on&unknownshow=on" target="_blank">{{ $allresult->name }}</a></td>

                                                   <td class="text-center">{{ number_format($allresult->item_count) }}</td>
                                                   
                                                   <td class="text-center">{{ $allresult->playlistownername }} (<a href="{{ $allresult->playlisturl }}" target="_blank">{{ $allresult->playlistname }}</a>)</td>
                                               
                                                   <td class="text-center">{{ date('d/m/Y H:i', $allresult->timestamp) }}</td>
                                                   
                                                  
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
var searchset='{{request()->input('searchset')}}';

</script>

@endif
@endif
@endsection
