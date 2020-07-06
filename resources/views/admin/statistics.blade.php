
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Basic Statistics</u></h2>
            <p class="text-muted text-center">(Statistics are generated every two hours.)</p>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span>Recaptcha Balance</span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->recaptchabalance,2) }} $</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
            <a href="{{ config('myconfig.config.server_url')  }}admin/genres" target="_blank"><span>Known Genres</span></a>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->uniquegenres) }}</h6>
                        </div>
                    </div>
                </div>
            </div>


            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
      <a href="{{ config('myconfig.config.server_url')  }}admin/localdatabase?searchset=1&pagenum=1&orderby=&title=&searchtype=artist&genres=&followers=0%3B100000000&claimedshow=on&claimedshow2=on&notclaimedshow=on&unknownshow=on&artistswithoutgenres=on" target="_blank"><span class="position-relative">Artists without genres<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists without any genres."></i></span></a>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->nogenreartists) }}</h6>
                        </div>
                    </div>
                </div>
            </div>


            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">Known Artists<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists in our database."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->allartists) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">Known Playlists<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Playlists in our database."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->allplaylists) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">Known Unclaimed Profiles<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists we know for sure that are unclaimed."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->knownunclaimed) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">Connected Spotify Accounts<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Added Spotify accounts which belong to a Spotifame user account, so they are connected."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->connectedaccounts) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">Generated Spotify Accounts<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Generated or added Spotify accounts which does not belong to a Spotifame user account, so they are not yet connected."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->generatedaccounts) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">Connected Artist Profiles<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists which are connected to a Spotify account, so we can control them through Spotifame.com."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->controlledartists) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">Connected Playlists<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Playlists which are connected to a Spotify account, so we can control them through Spotifame.com."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->controlledplaylists) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">New Artists Added Today<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="New artists added to our database today."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->newartiststoday) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">New Playlists Added Today<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="New playlists added to our database today."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->newplayliststoday) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">New Artists Added This Week<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="New artists added to our database this week."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->newartiststhisweek) }}</h6>
                        </div>
                    </div>
                </div>
            </div>


            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">New Playlists Added This Week<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="New playlists added to our database this week."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->newplayliststhisweek) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
           
            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">New Connected Accounts Added This Week<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists which were added this week and are connected to a Spotify account, so we can control them through Spotifame.com."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                        <h6>{{ number_format($statistics->newconnectionsthisweek) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            

           

            <h2 class="text-center mb-3"><u>More Statistics</u></h2>


            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-inline-block width200 pr-2 fontsize14">
                            <span class="position-relative">Generated Accounts by Countries<i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="You can see generated account count by country."></i></span>
                        </div>
                        <div class="d-inline-block width100">
                         <h4><a class="d-flex text-info align-bottom myopencollapse collapsed" data-toggle="collapse" href="#collapse-gen_acc_by_countries" aria-expanded="false" aria-controls="collapse-gen_acc_by_countries"><i class="mdi myopenicon"></i></a></h4>
                        </div>
                        <div id="collapse-gen_acc_by_countries" class="collapse" role="tabpanel" aria-labelledby="heading-gen_acc_by_countries" style="">
                        
                            <table class="table table-hover resulttable">
                                <thead>
                                  <tr>

                                    <th class="text-center width100">Country</th>

                                    <th class="text-center width100">Account Count</th>

  
                                  </tr>
                                </thead>
                                <tbody>

                @if(!empty($gen_acc_countries))

                            @foreach ($gen_acc_countries as $gen_acc_countries_s)
                        
                            <tr>
                                <td class="text-center">{{ $gen_acc_countries_s->country }}</td>
      
                             <td class="text-center">{{ number_format($gen_acc_countries_s->account_count) }}</td>
                                  
                          </tr>      

                            @endforeach
                                          @endif
              
                                            </tbody>
                                          </table>
                            <div class="text-right">
                                <a class="text-danger" href="#collapse-gen_acc_by_countries" data-target="#collapse-gen_acc_by_countries" data-toggle="collapse">Close</a>
                              </div>
                        </div>
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
