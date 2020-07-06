
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())
@if (auth()->user()->isAdmin() || auth()->user()->isEditor())


<div class="content-wrapper">
          <div class="row">
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Settings</u></h2>
                         
@if(session('success'))
   <div class="alert alert-success text-center">{{session('success')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
</div>     
@elseif(session('failed'))
   <div class="alert alert-danger text-center">{{session('failed')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
</div>
@endif  



    <form id="settingsform" action="{{ config('myconfig.config.server_url')  }}{{ Request::path() }}" method="post">  

        {{ csrf_field() }}

            <h4 class="mb-3">Basic Settings:</h4>

            @if(!empty($adminsettings))
                    @foreach ($adminsettings as $setting)

                        @if($setting->type == 'checkbox')

                        <div id="{{$setting->tablename}}|{{$setting->realname}}wrap">


                            <div class="form-check d-inline-block">
                              <label class="form-check-label">
                                <input name="{{$setting->tablename}}|{{$setting->realname}}" id="{{$setting->tablename}}|{{$setting->realname}}" type="checkbox" class="form-check-input"{!! $setting->realvalue ? ' checked="checked"' :''!!} autocomplete="off">
                                {{ $setting->fieldname }}
                              <i class="input-helper"></i></label>
                              
                              <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="{{ $setting->description }}"></i>
                              </div>


                        </div>


                        @elseif($setting->type == 'image')


                        @elseif($setting->type == 'colour')


                        @elseif($setting->type == 'select')
                   
                        <div class="form-group row">
                            <label for="{{$setting->tablename}}|{{$setting->realname}}" class="col-sm-3 col-form-label">{{ $setting->fieldname }}:</label>
                            <div class="col-sm-9">
                                <select class="form-control border-secondary" id="{{$setting->tablename}}|{{$setting->realname}}" name="{{$setting->tablename}}|{{$setting->realname}}" autocomplete="off" data-toggle="tooltip" data-placement="auto" title="" data-original-title="{{ $setting->description }}">
                                    {!! $setting->realvalue !!}
                                </select>
                            </div>
                        </div>

                        @else

                        <div class="form-group row">
                            <label for="{{$setting->tablename}}|{{$setting->realname}}" class="col-sm-3 col-form-label">{{ $setting->fieldname }}:</label>
                            <div class="col-sm-9">
                              <input type="text" class="form-control" value="{{ $setting->realvalue }}" id="{{$setting->tablename}}|{{$setting->realname}}" name="{{$setting->tablename}}|{{$setting->realname}}" autocomplete="off" data-toggle="tooltip" data-placement="auto" title="" data-original-title="{{ $setting->description }}">
                            </div>
                          </div>

                    
                        @endif

                    @endforeach
            @endif



            <h4 class="mb-4 mt-4 border-secondary border-top pt-4">Advanced Settings:</h4>

            

            @if(!empty($cronsettings))
            <div class="position-relative p-4 border border-secondary">
              <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Some Automated Settings</span>
                    @foreach ($cronsettings as $cronsetting)


                    <div id="cron|{{$cronsetting->name}}wrap">


                      <div class="form-check d-inline-block">
                        <label class="form-check-label">
                          <input name="cron|{{$cronsetting->name}}" id="cron|{{$cronsetting->name}}" type="checkbox" class="form-check-input"{!! $cronsetting->state ? ' checked="checked"' :''!!} autocomplete="off">
                          {{ $cronsetting->fieldname }}
                        <i class="input-helper"></i></label>
                        
                        <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="{{ $cronsetting->description }}"></i>
                        </div>


                  </div>


                    @endforeach
            </div>
            @endif

        

                    <button type="submit" class="btn btn-primary mt-2 float-right">Save Settings</button>

    </form>

        


                    
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
