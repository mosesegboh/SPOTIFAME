
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Our Created Groups</u></h2>
            <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">
                
                <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
                <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
                  <input type="hidden" class="form-control" name="orderby" id="orderby" value="">




             <button type="submit" class="btn btn-primary mt-2 float-right">Filter</button>

            </form>
            



                        </div>
                    

                        <div id="groups" class="col-lg-12 px-0">
                            <div class="col-12 mb-2 mt-2 position-relative">
                                
                         <p class="text-muted">{!! $paginationdisplay !!}</p>
                 
                            </div>
                 
                                     @if(!empty($groups))
                           <div class="col-lg-12 grid-margin">
                                           <div class="card results">
                                             <div class="card-body">
                 
                                           <div class="table-responsive">

       <div class="d-inline tabletopbuttons"><a href="#" class="addnewgroup" data-selectedtype=""
                                                ><i class="mdi mdi-plus-circle-outline align-middle mr-1"></i
                                                ><span>Add New Group</span></a></div>


                                             <table class="table table-hover resulttable">
                                               <thead>
                                                 <tr>

                                                  <th class="text-center width100">Group Name</th>

                                                  <th class="text-center width100">Description</th>

                                                  <th class="text-center width100">Type</th>
  
                                                  <th class="text-center width100">Note</th>

                                                  <th class="text-center width100">Added</th>
  
                                                  <th class="text-center width100">Item Count</th>

                                                  
  
                                                 </tr>
                                               </thead>
                                               <tbody>
                 
                                                 @if(!empty($groups))
                                             @foreach ($groups as $group)
                                             
              <tr id="parent_{{ $group->id }}" data-itemid="{{ $group->id }}" data-db-table="spotify_groups">
                                                
                                              

                         <td class="text-center position-relative pt-4">

             <span class="position-absolute actionbuttonswrap">
                        <span class="removebutton removegroupclick" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Remove the Group"><i class="mdi mdi-delete"></i></span>
             </span>
            
                          <p><a class="d-inline-block h4 fontsize14" href="{{ config('myconfig.config.server_url')  }}admin/group/{{ $group->id }}" target="_blank">{{ $group->name }}</a></p></td>

                         
                         <td class="text-center">{{ $group->description }}</td>

                         <td class="text-center">{{ ucfirst($group->type) }} Group</td>

                         <td class="text-center notefield myeditableelement" data-db-row="note" data-type="textarea" data-pk="" data-placeholder="Your notes here..." data-title="Enter notes">{!! $group->note !!}</td>
                         
                         <td class="text-center">{{ date('d/m/Y H:i', $group->timestamp) }}</td>

                         <td class="text-center">{{ $group->item_count }}</td>


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
