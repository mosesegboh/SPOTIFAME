
@extends('admin/beforelogin.main')

@section('content')


<div class="container-fluid page-body-wrapper">
  <div class="content-wrapper">
      <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-body">

            <h2 class="text-center mb-3"><u>Connect Spotify Accounts</u></h2>
           

      <div id="addaccountwrap" class="position-relative p-4 border border-secondary">

        <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Connect Your Account</span>

            @if($hash!='' && $okshow=='1')

         

                <div>
                <p>1.) Login to Spotify with your account:</p>
                <a class="btn btn-primary btn-sm" href="https://artists.spotify.com/" target="_blank">
                    <i class="mdi mdi-spotify align-middle d-inline-flex mr-1"></i><span>Login</span>
                </a>
                </div>

                <div class="mt-2">
                    <p>2.) Click on this button and grant the website access:</p>

                <a class="btn btn-primary btn-sm" href="{{ config('myconfig.config.server_url')  }}grantspotifyaccess?hash={{$hash}}">
                    <i class="mdi mdi mdi-lock-open align-middle d-inline-flex mr-1"></i><span>Grant Access</span>
                </a>

                @if(urldecode(request()->input('msg'))=='alreadyaddedbutupdating')
                <p class="text-success">Thank you it was successful. Data refreshed.</p>
                @endif

                @if(urldecode(request()->input('msg'))=='successfullyadded')
                <p class="text-success">Thank you it was successful.</p>
                @endif

                @if(urldecode(request()->input('msg'))=='hashnotcorrect')
                <p class="text-danger">The link hash is not correct!</p>
                @endif

                @if(urldecode(request()->input('msg'))=='notanartist')
                <p class="text-danger">You are not logged in as an artist!</p>
                @endif

                @if(urldecode(request()->input('msg'))=='cantaddown')
                <p class="text-danger">You can't add yourself as an artist to your manager account.</p>
                @endif

            </div>

            @else

              @if(urldecode(request()->input('msg'))=='alreadyaddedbutupdating')
                <p class="text-success">Thank you it was successful. Data refreshed.</p>
                @endif

                @if(urldecode(request()->input('msg'))=='successfullyadded')
                <p class="text-success">Thank you it was successful.</p>
                @endif

              @if($hash!='')
              <p class="text-danger">The link is not correct!</p>
              @endif







            @endif

            @if (session()->has('error'))
                    @if(is_array(session()->get('error')))
                        @foreach (session()->get('error') as $message)
                        <p class="alert alert-danger mt-3">{{ $message }}</p>
                        @endforeach
                    @else
                        <p class="alert alert-danger mt-3">{{ session()->get('error') }}</p>
                    @endif
                @endif
              
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

@endsection
