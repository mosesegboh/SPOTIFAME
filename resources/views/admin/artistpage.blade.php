
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-lg-12">
                                  <div class="border-bottom text-center pb-4">
                                  <img src="{{ $theartist->imageurl ? $theartist->imageurl : 'https://via.placeholder.com/92x92'}}" alt="profile" class="img-lg mb-3"/>
                                    <div class="mb-3">
                                      <h3>{{ $theartist->name }}</h3>
                                    </div>

                                    <div class="mx-auto mb-3 maxwidth300" style="max-width:300px;">
                                    <div class="d-flex justify-content-between mt-2">
                                        <small>Popularity</small>
                                        <small>{{ $theartist->popularity }}/100</small>
                                      </div>
                                      <div class="progress progress-sm mt-2">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $theartist->popularity }}%" aria-valuenow="{{ $theartist->popularity }}" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </div>
                                   
                                    <p class="w-75 mx-auto mb-3">Followers: {{ number_format($theartist->followercount) }}</p>
                                  </div>
                                  @if(!empty($genres))
                                  <div class="border-bottom py-4">
                                    <p>Genres</p>
                                    <div>
                                        @foreach ($genres as $genres_s)
                                    <label class="badge badge-outline-light">{{ $genres_s }}</label>
                                        @endforeach
                                    </div>                                                               
                                  </div>
                                  @endif
                                  <div class="py-4">
                                    <p class="clearfix">
                                        <span class="float-left">
                                          Added
                                        </span>
                                        <span class="float-right text-muted">
                                            {{ date('d/m/Y H:i', $theartist->timestamp) }}
                                        </span>
                                     </p>
                                    <p class="clearfix">
                                      <span class="float-left">
                                        Claimed Status
                                      </span>
                                      <span class="float-right text-muted">
                                        {{ $theartist->claimed=='1' ? 'Claimed' : ($theartist->claimed=='2' ? 'Not Claimed' : ( $theartist->claimed=='3' ? 'Claimed (changed)' : 'Unknown')) }}   
                                      </span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">
                                          Color Code
                                        </span>
                                        <span class="float-right text-muted" style="width:25px;height:25px;background-color:{{ $theartist->colorcode ? $theartist->colorcode : '#FFF' }}">
                                        </span>
                                      </p>
                                    <p class="clearfix">
                                        <span class="float-left">
                                         Distributor
                                        </span>
                                        <span class="float-right text-muted">
                                            @if ($theartist->distributorurl !='')
                                            <a href="{{ $theartist->distributorurl }}" target="_blank">{{ $theartist->distributorname ? $theartist->distributorname : 'Unkown' }}</a>
                                            @else
                                            {{ $theartist->distributorname ? $theartist->distributor : 'Unkown' }}
                                            @endif
                                        </span>
                                      </p>
                                    <p class="clearfix">
                                      <span class="float-left">
                                        Spotify
                                      </span>
                                      <span class="float-right text-muted">
                                        <a href="{{ $theartist->url }}" target="_blank">{{ $theartist->name }}</a>
                                      </span>
                                    </p>
                                  </div>
                                  
                                </div>

                              </div>

                    
                        </div>
                    
       
                    </div>  
                  </div>
            </div>
</div>
<!-- content-wrapper ends -->

<script>


</script>

@endif
@endif
@endsection
