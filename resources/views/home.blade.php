
@extends('admin/beforelogin.main')

@section('content')

<div class="container-fluid page-body-wrapper">
    <div class="main-home-panel">
        <div class="content-wrapper">

            <div class="col-lg-12 grid-margin">

                <div class="row">
                    <div class="myhrline mb-5 mt-3 mx-auto">
                    </div>
                </div>

                    <div class="row">
     <h3 class="text-justify letterspacing2 text-center w-100 text-uppercase text-white myplateiaboldfont fontsize28 mb-4">Global Promotion and management tool</h3>

     <h3 class="text-justify letterspacing2 text-center w-100 text-uppercase text-white myplateiaboldfont fontsize16">For artists, playlist editors, journalists, managers and labels</h3>

                    </div>

                    <div class="row">
                        <div class="myhrline mb-3 mt-5 mx-auto">
                        </div>
                    </div>
            </div>

          
     <div class="grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="row">
<div class="col-lg-6 stretch-card">
<ul class="mb-0 list-unstyled">
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>pitch music directly to thousands of playlist editors</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>promote and manage Spotify artist profiles</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>get state of the art statistics on stream and follower development</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>see who is listening to music and target similar fans</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>optimize routine tasks with artificial intelligence</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>get search functions with complex data processing (big data)</i></span>
    </li>
</ul> 
</div>

<div class="col-lg-6 stretch-card">
<ul class="mb-0 list-unstyled">
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>identify remixers / collaborators</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>communicate with fans</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>send music directly to DJs accross the globe</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>benchmark music to artists in similar genres</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>get overview of airplay charts and global rankings</i></span>
    </li>
    <li>
        <span class="width18 h-auto mr-2 d-inline-block"><img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/smalldisc.png" /></span>
        <span class="text-white"><i>screen music for A&R professionals</i></span>
    </li>
</ul>
</div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="row">

        <div class="col-lg-4 grid-margin">   

                <div class="homebubble col-lg-6 h-auto bg-success p-2 float-left">
                    <span class="iconstyle text-center rounded-circle d-inline-block bg-white text-success fontsize26 lineheight65"><i class="icon-playlist"></i></span>
                    <span class="w-100 text-center d-inline-block">Spotify Playlist Network</span>
                    <span class="number w-100 text-center d-inline-block h1 font-weight-normal mb-0">{{ number_format($homepagestats->playlistscount,0,",",".") }}</span>
                    <span class="w-100 text-center d-inline-block">playlists</span>
                </div>
        </div>
        <div class="col-lg-4 grid-margin">   

                <div class="homebubble col-lg-6 h-auto bg-info p-2 mx-auto">
                    <span class="iconstyle text-center rounded-circle d-inline-block bg-white text-info fontsize26 lineheight65"><i class="icon-people"></i></span>
                    <span class="w-100 text-center d-inline-block">Consolidated Followers</span>
                    <span class="number w-100 text-center d-inline-block h1 font-weight-normal mb-0">{{ \App\Helpers\AppHelper::instance()->numberFormat($homepagestats->consolidatedfollowers/1000000,1,",",".") }} mil</span>
                    <span class="w-100 text-center d-inline-block">fans</span>
                </div>

        </div>
        <div class="col-lg-4 grid-margin">   

            <div class="homebubble col-lg-6 h-auto bg-danger p-2 float-right">
                <span class="iconstyle text-center rounded-circle d-inline-block bg-white text-danger fontsize26 lineheight65"><i class="icon-earphones-alt"></i></span>
                <span class="w-100 text-center d-inline-block">Ongoing Track Promotions</span>
                <span class="number w-100 text-center d-inline-block h1 font-weight-normal mb-0">{{ number_format($homepagestats->trackpromotions,0,",",".") }}</span>
                <span class="w-100 text-center d-inline-block">tracks</span>
            </div>

        </div>

    </div>
</div>

<div class="col-lg-12 grid-margin">
    <div class="row">
        <h4 class="letterspacing2 fontsize20 text-uppercase text-center w-100 text-white"><b>Spotifame is used by thousands of major artists and thousands of worldwide playlist editors</b></h4>
    </div>
</div>

<div class="col-lg-12 grid-margin">
    <div class="row">
        <div class="w-100 h-auto">
        <img class="w-100 h-auto" src="{{ config('myconfig.config.server_url')  }}images/homebottomimage.jpg" />
        </div>
    </div>
</div>

<div class="grid-margin">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Playlist genres</h4>
                <div class="mx-auto widthmax500">
                    <canvas id="playlistGenresChart" data-currentchartid="0"></canvas>
                </div>
        </div>
    </div>
</div>  

<div class="col-lg-12 grid-margin">
    <div class="row">
        <p class="text-center text-white letterspacing2">
Spotifame’s initial core structure was made possible by a grant from the The Nordic Council of Ministers. As of 2020 our innovative service is operated as a nonprofit operation and is completely free for artists. Spotifame has no corporate affiliation with Spotify.
        </p>
    </div>
</div>

                </div>
            </div>
</div>

<script>
var genreresultset=JSON.parse("{!!$genreresultset!!}");

</script>


@endsection