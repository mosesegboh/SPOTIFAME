
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Contact (letters)</u></h2>
            <form id="searchform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="get" autocomplete="off">
                
                <input type="hidden" class="form-control" name="searchset" id="searchset" value="1">
                <input type="hidden" class="form-control" name="pagenum" id="pagenum" value="1">
                  <input type="hidden" class="form-control" name="orderby" id="orderby" value="">


                  <div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Filter Results:</h4>
                            
    
                    <div class="form-group d-flex">
                      <input name="name" id="name" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Name of User..." value="{{ urldecode(request()->input('name')) }}" autocomplete="on">
                              
                     </div>


                       <div class="form-group d-flex">
                        <input name="email" id="email" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Email of User..." value="{{ urldecode(request()->input('email')) }}" autocomplete="on">
                                
                       </div>

                     
    
                            <div class="form-group row">
                                <label for="type" class="lineheight24rem">Type:</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary" id="type" name="type" autocomplete="off">
                                        <option value=""{{ urldecode(request()->input('type'))=='' ? ' selected="selected"' :''}}>- All Letters -</option>
                                        <option value="contact"{{ urldecode(request()->input('type'))=='contact' ? ' selected="selected"' :''}}>- Contact letters -</option>
                                        <option value="homecontact"{{ urldecode(request()->input('type'))=='homecontact' ? ' selected="selected"' :''}}>- Home Form Letters -</option>
                                    </select>
                                </div>
                             </div>





    
    
                            <button type="submit" class="btn btn-primary mt-2 float-right">Filter</button>
    
                        </div>
                    </div>
                </div>
                  





            </form>
            



                        </div>
                    

                        <div id="letters" class="col-lg-12 px-0">
                            <div class="col-12 mb-2 mt-2 position-relative">
                                <h2>Results:</h2>
                         <p class="text-muted">{!! $paginationdisplay !!}</p>
                 
                            </div>
                 
                                     @if(!empty($letters))
                           <div class="col-lg-12 grid-margin">
                                           <div class="card results">
                                             <div class="card-body">
                 
                                           <div class="table-responsive">

                                             <table class="table table-hover resulttable">
                                               <thead>
                                                 <tr>
                                                 
                                                   <th class="text-center width100">Name</th>

                                                   <th class="text-center width100">Email</th>

                                                   <th class="text-center width100">ArtistName</th>

                                                   <th class="text-center width100">Phone</th>

                                                   <th class="text-center width100">Added</th>

                                                   <th class="text-center width100">User (if registered)</th>

                                                   <th class="text-center width100">Type</th>
                                                   
                                                   <th class="text-center width100">Subject</th>

                                                   <th class="text-center width100">Content</th>

                                                   

                                                   
                                                   

                                                 </tr>
                                               </thead>
                                               <tbody>
                 
                                                 @if(!empty($letters))
                                             @foreach ($letters as $letters)
                                             
                                             <tr id="parent_{{ $letters->id }}" data-itemid="{{ $letters->id }}" data-db-table="spotify_contact">
                                                
                         <td class="text-center">{{  $letters->name }}</td>
                         <td class="text-center">{{  $letters->email }}</td>
                         <td class="text-center">{{  $letters->djname }}</td>
                         
                         <td class="text-center">{{  $letters->phone }}</td>
                         <td class="text-center">{{  Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</td>

                         <td class="text-center"><a target="_blank" href="{{ config('myconfig.config.server_url')  }}admin/users?searchset=1&pagenum=1&username={{ $letters->username }}&advancedsearch=1"
                            >{{  $letters->username }}</td>

                         <td class="text-center">{{  $letters->type }}</td>

                         <td class="text-center">{{  $letters->subject }}</td>

                         <td class="text-center">{{  $letters->description }}</td>
                  

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
