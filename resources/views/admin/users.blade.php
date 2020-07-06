
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Users</u></h2>
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

                     
    
                            <div class="form-group row">
                                <label for="maintype" class="lineheight24rem">Main Type:</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary" id="maintype" name="maintype" autocomplete="off">
                                        <option value=""{{ urldecode(request()->input('maintype'))=='' ? ' selected="selected"' :''}}>- All Options -</option>
                                       
                                        @foreach ($userlevels as $userlevel)
                                            @if($userlevel->rolelevel<10)
                                            <option value="{{ $userlevel->rolename }}"{{ urldecode(request()->input('maintype'))==$userlevel->rolename ? ' selected="selected"' :''}}>{{ $userlevel->rolename }}</option>
                                            @endif
                                        @endforeach

                                        <option value="user"{{ urldecode(request()->input('maintype'))=='user' ? ' selected="selected"' :''}}>user</option>
                                    </select>
                                </div>
                             </div>


                             <div id="subtypewrap" class="form-group row">
                                <label for="subtype" class="lineheight24rem">Sub Type:</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary" id="subtype" name="subtype" autocomplete="off">
                                        <option value=""{{ urldecode(request()->input('subtype'))=='' ? ' selected="selected"' :''}}>- All Options -</option>
                                       
                                        @foreach ($userlevels as $userlevel)
                                            @if($userlevel->rolelevel>=10)
                                            <option value="{{ $userlevel->rolename }}"{{ urldecode(request()->input('subtype'))==$userlevel->rolename ? ' selected="selected"' :''}}>{{ $userlevel->rolename }}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                </div>
                             </div>
   
 <div>
    <div class="form-check d-inline-block">
        <label class="form-check-label" data-toggle="collapse" href="#advancedsearchwrap" aria-expanded="false" aria-controls="advancedsearchwrap">
         <input name="advancedsearch" id="advancedsearch" type="checkbox" class="form-check-input"{!! (urldecode(request()->input('advancedsearch'))=='1') ? ' checked="checked"' :''!!} autocomplete="off" value="1">
                                             Activate/Disable Extra Filters
                                      <i class="input-helper"></i></label>

                                </div>
                        
 </div>
             

<div id="advancedsearchwrap" role="tabpanel" aria-labelledby="advancedsearchwrap" class="collapse{{ urldecode(request()->input('advancedsearch'))=='1' ? ' show' :''}} advancedsearch position-relative p-4 mt-4 mb-3 border border-secondary"> <!-- spotify search part -->

                  <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Extra Filters</span>            

<p>If hidden (deactivated), these filters won't be used!</p>
                             <div class="form-group d-flex advancedfield">
                                <input name="username" id="username" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Username of User..." value="{{ urldecode(request()->input('username')) }}" autocomplete="on">
                                        
                               </div>
        
        
                               <div class="form-group d-flex advancedfield">
                                <input name="email" id="email" type="text" class="form-control border-secondary pl-2 pr-2 topinputfield" placeholder="Email of User..." value="{{ urldecode(request()->input('email')) }}" autocomplete="on">
                                        
                               </div>


                               

                  <div class="form-group row">
                                <label for="maintype" class="lineheight24rem">Is artist?:</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary width100" id="isartist" name="isartist" autocomplete="off">
                                        <option value=""{{ urldecode(request()->input('isartist'))=='' ? ' selected="selected"' :''}}>- Both -</option>
                                        <option value="1"{{ urldecode(request()->input('isartist'))=='1' ? ' selected="selected"' :''}}>Yes</option>
                                        <option value="0"{{ urldecode(request()->input('isartist'))=='0' ? ' selected="selected"' :''}}>No</option>
                                    </select>
                                </div>
                  </div>

                  <div class="form-group row">
                                <label for="maintype" class="lineheight24rem">Is label?:</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary width100" id="islabel" name="islabel" autocomplete="off">
                                        <option value=""{{ urldecode(request()->input('islabel'))=='' ? ' selected="selected"' :''}}>- Both -</option>
                                        <option value="1"{{ urldecode(request()->input('islabel'))=='1' ? ' selected="selected"' :''}}>Yes</option>
                                        <option value="0"{{ urldecode(request()->input('islabel'))=='0' ? ' selected="selected"' :''}}>No</option>
                                    </select>
                                </div>
                  </div>

                  <div class="form-group row">
                    <label for="maintype" class="lineheight24rem">Is manager?:</label>
                    <div class="col-sm-9">
                        <select class="form-control border-secondary width100" id="ismanager" name="ismanager" autocomplete="off">
                            <option value=""{{ urldecode(request()->input('ismanager'))=='' ? ' selected="selected"' :''}}>- Both -</option>
                            <option value="1"{{ urldecode(request()->input('ismanager'))=='1' ? ' selected="selected"' :''}}>Yes</option>
                            <option value="0"{{ urldecode(request()->input('ismanager'))=='0' ? ' selected="selected"' :''}}>No</option>
                        </select>
                    </div>
                </div>


                    <div class="form-group row">
                        <label for="maintype" class="lineheight24rem">Is playlistowner?:</label>
                        <div class="col-sm-9">
                            <select class="form-control border-secondary width100" id="isplaylistowner" name="isplaylistowner" autocomplete="off">
                                <option value=""{{ urldecode(request()->input('isplaylistowner'))=='' ? ' selected="selected"' :''}}>- Both -</option>
                                <option value="1"{{ urldecode(request()->input('isplaylistowner'))=='1' ? ' selected="selected"' :''}}>Yes</option>
                                <option value="0"{{ urldecode(request()->input('isplaylistowner'))=='0' ? ' selected="selected"' :''}}>No</option>
                            </select>
                        </div>
                </div>

                <div class="form-group row">
                    <label for="maintype" class="lineheight24rem">Is journalist?:</label>
                    <div class="col-sm-9">
                        <select class="form-control border-secondary width100" id="isjournalist" name="isjournalist" autocomplete="off">
                            <option value=""{{ urldecode(request()->input('isjournalist'))=='' ? ' selected="selected"' :''}}>- Both -</option>
                            <option value="1"{{ urldecode(request()->input('isjournalist'))=='1' ? ' selected="selected"' :''}}>Yes</option>
                            <option value="0"{{ urldecode(request()->input('isjournalist'))=='0' ? ' selected="selected"' :''}}>No</option>
                        </select>
                    </div>
            </div>

            <div class="form-group row">
                <label for="maintype" class="lineheight24rem">Is dj/remixer?:</label>
                <div class="col-sm-9">
                    <select class="form-control border-secondary width100" id="isdjremixer" name="isdjremixer" autocomplete="off">
                        <option value=""{{ urldecode(request()->input('isdjremixer'))=='' ? ' selected="selected"' :''}}>- Both -</option>
                        <option value="1"{{ urldecode(request()->input('isdjremixer'))=='1' ? ' selected="selected"' :''}}>Yes</option>
                        <option value="0"{{ urldecode(request()->input('isdjremixer'))=='0' ? ' selected="selected"' :''}}>No</option>
                    </select>
                </div>
        </div>

                <div class="form-group row">
                                <label for="maintype" class="lineheight24rem">Generated?:</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary width100" id="generated" name="generated" autocomplete="off" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Users based on their creation: normal or generated by our script.">
                                        <option value=""{{ urldecode(request()->input('generated'))=='' ? ' selected="selected"' :''}}>- Both -</option>
                                        <option value="1"{{ urldecode(request()->input('generated'))=='1' ? ' selected="selected"' :''}}>Yes</option>
                                        <option value="0"{{ urldecode(request()->input('generated'))=='0' ? ' selected="selected"' :''}}>No</option>
                                    </select>
                                </div>
                  </div>

                  <div class="form-group row">
                                <label for="maintype" class="lineheight24rem">Verified?:</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary width100" id="verified" name="verified" autocomplete="off" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Users who verified/haven't verified their email addresses.">
                                        <option value=""{{ urldecode(request()->input('verified'))=='' ? ' selected="selected"' :''}}>- Both -</option>
                                        <option value="1"{{ urldecode(request()->input('verified'))=='1' ? ' selected="selected"' :''}}>Yes</option>
                                        <option value="0"{{ urldecode(request()->input('verified'))=='0' ? ' selected="selected"' :''}}>No</option>
                                    </select>
                                </div>
                  </div>



    </div>

    
    
                            <button type="submit" class="btn btn-primary mt-2 float-right">Filter</button>
    
                        </div>
                    </div>
                </div>
                  





            </form>
            



                        </div>
                    

                        <div id="users" class="col-lg-12 px-0">
                            <div class="col-12 mb-2 mt-2 position-relative">
                                <h2>Results:</h2>
                         <p class="text-muted">{!! $paginationdisplay !!}</p>
                 
                            </div>
                 
                                     @if(!empty($users))
                           <div class="col-lg-12 grid-margin">
                                           <div class="card results">
                                             <div class="card-body">
                 
                                           <div class="table-responsive">

                                             <table class="table table-hover resulttable">
                                               <thead>
                                                 <tr>
                                                   <th class="text-center width100">UserName</th>

                                                   <th class="text-center width100">Name</th>

                                                   <th class="text-center width100">Email</th>

                                                   <th class="text-center width100">Notes</th>
                                                   
                                                   <th class="text-center width100">MainType</th>
                                                   <th class="text-center width100">SubType</th>
                                                   <th class="text-center width100">Created</th>

                                                   <th class="text-center width100">Artist?</th>
                                                   <th class="text-center width100">Label?</th>
                                                   <th class="text-center width100">Manager?</th>
                                                   <th class="text-center width100">Playlistowner?</th>
                                                   <th class="text-center width100">DJ/Remixer?</th>
                                                   <th class="text-center width100">Journalist?</th>

                                                   <th class="text-center width100">Verified?</th>
                                                   <th class="text-center width100">Generated?</th>

                                                   
                                                   

                                                 </tr>
                                               </thead>
                                               <tbody>
                 
                                                 @if(!empty($users))
                                             @foreach ($users as $user)
                                             
                                             <tr id="parent_{{ $user->id }}" data-itemid="{{ $user->id }}" data-db-table="users">
                                                
                                              
                         <td class="text-center">{{  $user->username }}</td>
                         <td class="text-center">{{  $user->name }}</td>
                         <td class="text-center">{{  $user->email }}</td>

                         <td class="text-center notefield myeditableelement" data-db-row="note" data-type="textarea" data-pk="" data-placeholder="Your notes here..." data-title="Enter notes">{!! $user->note !!}</td>

                         <td class="text-center">{{  $user->isuser ? 'user' : $user->type }}</td>
                         <td class="text-center{{  $user->isuser ? ' subtypefield' : '' }}">{{  $user->isuser ? $user->type : '' }}</td>
                         <td class="text-center">{{  Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</td>
                  

                            


                         <td class="text-center">{!! $user->isartist ? '<label class="badge badge-success">Yes</label>' : '<label class="badge badge-danger">No</label>' !!}</td>
                         <td class="text-center">{!!  $user->islabel ? '<label class="badge badge-success">Yes</label>' : '<label class="badge badge-danger">No</label>' !!}</td>
                         <td class="text-center">{!!  $user->ismanager ? '<label class="badge badge-success">Yes</label>' : '<label class="badge badge-danger">No</label>' !!}</td>
                         <td class="text-center">{!!  $user->isplaylistowner ? '<label class="badge badge-success">Yes</label>' : '<label class="badge badge-danger">No</label>' !!}</td>
                         <td class="text-center">{!!  $user->isremixer ? '<label class="badge badge-success">Yes</label>' : '<label class="badge badge-danger">No</label>' !!}</td>
                         <td class="text-center">{!!  $user->isjournalist ? '<label class="badge badge-success">Yes</label>' : '<label class="badge badge-danger">No</label>' !!}</td>
                         

                         <td class="text-center">{!!  $user->email_verified_at ? Carbon\Carbon::parse($user->email_verified_at)->format('d/m/Y H:i') : '<label class="badge badge-danger">No</label>' !!}</td>

                         <td class="text-center">{!!  $user->generated ? '<label class="badge badge-success">Yes</label>' : '<label class="badge badge-danger">No</label>' !!}</td>

                         
                 
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
