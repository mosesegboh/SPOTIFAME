
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                          <div id="parent_{{ $checkIfTurnedOn->id }}" data-itemid="{{ $checkIfTurnedOn->id }}" data-db-table="spotify_cron_setter" data-db-row="state" class="d-inline-block" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Turn ON/OFF the processing of Spotify Searches.">
                            <input id="turnmainonoff" type="checkbox" {!! $checkIfTurnedOn->state==1 ? 'checked="checked"' : '' !!} data-toggle="toggle" data-size="sm" autocomplete="off">
                         
                            </div>

            <h2 class="text-center mb-3"><u>Searches In Progress</u></h2>
            <p class="text-muted text-center">Below you can see the currently processing/processed searches for artists and playlists</p>
                    


            <div id="searchresults" class="col-lg-12 px-0">
                <div class="col-12 mb-5 mt-5">
                                 


     
                               </div>
     
                         @if(!empty($processedresults))
               <div class="col-lg-12 grid-margin">
                               <div class="card results">
                                 <div class="card-body">
     
   @if (request()->input('getcurrentid')=='')
     <form id="inprogressform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get">                         
      
      
        <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
        <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
 
        

        <div id="stateinprogwrap" class="position-relative p-4 border border-secondary">

<span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Filter Results By State</span>

        <div>
        <div class="form-check d-inline-block">
        <label class="form-check-label successful-bg">
        <input name="processedshow" id="processedshow" type="checkbox" class="form-check-input"{!! ($processedshow=='on') ? ' checked="checked"' :''!!} autocomplete="off">
                      Processed
              <i class="input-helper"></i></label>
        </div>

    </div>

        <div>
        <div class="form-check d-inline-block">
            <label class="form-check-label waiting-bg">
            <input name="waitingshow" id="waitingshow" type="checkbox" class="form-check-input"{!! ($waitingshow=='on') ? ' checked="checked"' :''!!} autocomplete="off">
                     Waiting (queued)
                  <i class="input-helper"></i></label>
            </div>

        </div>

        <div>
            <div class="form-check d-inline-block">
                <label class="form-check-label processing-bg">
                <input name="processingshow" id="processingshow" type="checkbox" class="form-check-input"{!! ($processingshow=='on') ? ' checked="checked"' :''!!} autocomplete="off">
                              Processing
                      <i class="input-helper"></i></label>
                </div>
            
            </div>


            <div>
            <div class="form-check d-inline-block">
                    <label class="form-check-label problem-bg">
                    <input name="problematicshow" id="problematicshow" type="checkbox" class="form-check-input"{!! ($problematicshow=='on') ? ' checked="checked"' :''!!} autocomplete="off">
                               Paused
                          <i class="input-helper"></i></label>
                    </div>

                </div>

    </div>
    

     </form>

     @endif

                                   
                                
                               <div class="table-responsive">

                                @if (request()->input('getcurrentid')!='')
                                <p><span>Filtered result by ID:</span><span class="ml-2">{{ request()->input('getcurrentid') }}</span></p>
                                @endif

                                @if(!empty($processedresults))
                                <p class="text-muted mt-2">{!! $paginationdisplay !!}
                                  @endif
                                  
                                 <table class="table table-hover resulttable myeditabletable">
                                   <thead>
                                     <tr>
                                       <th class="text-center width100">ID</th>
     
                                       <th class="text-center width120">State</th>
                                       
                                       <th class="text-center width120">Type</th>
                                       
                                       <th class="text-center width120">Added</th>

                                       <th class="text-center width120">Searchstring</th>

                                       <th class="text-center width120">Item Count</th>
                                      
                                     </tr>
                                   </thead>
                                   <tbody>
     
                                     @if(!empty($processedresults))
                                 @foreach ($processedresults as $processedresult)
                                     <tr id="parent_{{ $processedresult->id }}" data-itemid="{{ $processedresult->id }}" data-db-table="spotify_search_cache" {!! $processedresult->inprogress=='0' ? 'class="myeditablerow successful-bg"' : ($processedresult->inprogress==1 ? 'class="myeditablerow waiting-bg"' : ($processedresult->inprogress==2 ? 'class="myeditablerow processing-bg"' : 'class="myeditablerow problem-bg"'))!!}>
                                                           
                                       <td class="text-center">
                                        <a href="{{ config('myconfig.config.server_url')  }}admin/search{{ $processedresult->url }}" target="_blank">
                                            {{ $processedresult->id }}
                                        </a>
                                       </td>

                                       <td class="text-center position-relative inprogress myeditableelement" data-db-row="inprogress" data-type="select" data-pk="" data-value="{{ $processedresult->inprogress }}">{{ $processedresult->inprogress=='0' ? 'Processed!' : ($processedresult->inprogress==1 ? 'Waiting (queued)...' : ($processedresult->inprogress==2 ? 'Now Processing...' : 'Paused!'))}}</td>
                                       
                                       <td class="text-center">{{ $processedresult->searchtype }}</td>

                                       <td class="text-center">{{ date('d/m/Y H:i', $processedresult->timestamp) }}</td>

                                       <td class="text-center">{{ $processedresult->searchstring }}</td>

                                       <td class="text-center">{{ $processedresult->item_count }}</td>
                                       
     
                                     </tr>
                                     
     
     
     
     
                                     @endforeach
                                 @endif
     
                                   </tbody>
                                 </table>

                                 @if (request()->input('getcurrentid')!='')
                                 <div class="text-center mt-3">
                                   <a href="{{ config('myconfig.config.server_url')  }}admin/searchesinprogress">SHOW ALL CACHED SEARCHES</a>
                                </div>
                                 @endif

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
</div>
<!-- content-wrapper ends -->

<script>
var csrf_token='{{ csrf_token() }}';

</script>

@endif
@endif
@endsection
