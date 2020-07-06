<!DOCTYPE html>
<html lang="en">
<head>
<!--*****************************META*********************************-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>{{ $meta['title'] }}</title>
	<meta name="description" content="{{ $meta['description'] }}" />
    <meta name="keywords" content="{{ $meta['keywords'] }}" />
    
    @if (Request::is('/'))
	<!--if index-->
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ config('myconfig.config.sitename_caps')  }}" />
    <meta property="og:url" content="{{ $meta['url'] }}" />
    <meta property="og:title" content="{{ $meta['title'] }}" />
    <meta property="og:description" content="{{ $meta['description'] }}" />
    <meta property="og:image" content="{{ $meta['image'] }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="{{ $meta['title'] }}" />
    <meta name="twitter:description" content="{{ $meta['description'] }}" />
    <meta name="twitter:domain" content="{{ config('myconfig.config.sitename_caps')  }}" />
    <meta name="twitter:image:src" content="{{ $meta['image'] }}" />
    <!--if index-->
    @endif
    
    <!-- plugins:css -->
  <link rel="stylesheet" href="{{ config('myconfig.config.server_url')  }}vendors/iconfonts/mdi/font/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="{{ config('myconfig.config.server_url')  }}vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="{{ config('myconfig.config.server_url')  }}vendors/css/vendor.bundle.addons.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="{{ config('myconfig.config.server_url')  }}vendors/iconfonts/font-awesome/css/font-awesome.min.css" />
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ config('myconfig.config.server_url')  }}css/vertical-layout-dark/style.css">
  <!-- endinject -->
  <link rel="apple-touch-icon" sizes="180x180" href="{{ config('myconfig.config.server_url')  }}apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ config('myconfig.config.server_url')  }}favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ config('myconfig.config.server_url')  }}favicon-16x16.png">
  <link rel="manifest" href="{{ config('myconfig.config.server_url')  }}site.webmanifest">
  <link rel="mask-icon" href="{{ config('myconfig.config.server_url')  }}safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#131633">
  <meta name="theme-color" content="#ffffff">
  
  
  <!-- plugins:js -->
<script src="{{ config('myconfig.config.server_url')  }}vendors/js/vendor.bundle.base.js"></script>
  <script src="{{ config('myconfig.config.server_url')  }}vendors/js/vendor.bundle.addons.js"></script>
  <!-- endinject -->
  
	<script type="text/javascript">
		var base = '{{ config('myconfig.config.server_url')  }}';
		var userid = '{{ session('ans')['userid'] }}';
    var curpage = '{{ Request::path() }}';

    var artistlastgroupname = '{{ Session::get('artistlastgroupname') }}';
    var artistlastgroupid = '{{ Session::get('artistlastgroupid') }}';
    var playlistlastgroupname = '{{ Session::get('playlistlastgroupname') }}';
    var playlistlastgroupid = '{{ Session::get('playlistlastgroupid') }}';
    var tracklastgroupname = '{{ Session::get('tracklastgroupname') }}';
    var tracklastgroupid = '{{ Session::get('tracklastgroupid') }}';
	</script>
    
<script type="text/javascript">


</script>
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>
<body>

      <div class="pleasewait d-none" id="defaultloading">
        <div class="pleasewaitholder pleasewaitholder2">
    
          <div class="">
            <div class="circle-loader" alt="Please Wait..."></div>
          </div>
    
      </div>
      </div>
      <div class="hidden" id="sendingdata" data-sending='0'></div>
      
<div class="container-scroller">
    <!-- partial:partials/_navbar.html -->

<header>
<!-- Navigation -->
    
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      <a class="navbar-brand brand-logo" href="{{ config('myconfig.config.server_url')  }}admin"><img src="{{ config('myconfig.config.server_url')  }}images/logo.png" alt="logo"/></a>
      <a class="navbar-brand brand-logo-mini" href="{{ config('myconfig.config.server_url')  }}admin"><img src="{{ config('myconfig.config.server_url')  }}images/logo.png" alt="logo"/></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
         
      <ul class="navbar-nav navbar-nav-right">
        
        <li class="nav-item nav-profile dropdown mr-0 mr-sm-2">
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
            <img src="https://via.placeholder.com/40x40" alt="profile"/>
            <span class="nav-profile-name">{{ auth()->user()->username }}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
            <a class="dropdown-item" href="{{ config('myconfig.config.server_url')  }}admin/profile">
              <i class="mdi mdi-settings text-primary"></i>
              Settings
            </a>
            <a class="dropdown-item" href="{{ route('logout') }}">
              <i class="mdi mdi-logout text-primary"></i>
              Logout
            </a>
          </div>
        </li>
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
      </button>
    </div>
  </nav>
  </header>

<!-- partial -->
<div class="container-fluid page-body-wrapper">
<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav sidenav">
          <li class="nav-item{{Request::path() =='admin' ? ' active' : '' }}">
            <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin">
              <i class="mdi mdi-home menu-icon"></i>
              <span class="menu-title">Home</span>
            </a>
          </li>

          @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
          <li class="nav-item{{Request::path() =='admin/settings' ? ' active' : '' }}">
            <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/settings">
              <i class="mdi mdi-settings menu-icon"></i>
              <span class="menu-title">Settings</span>
            </a>
          </li>
          @endif
          
          @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
          <li class="nav-item{{Request::path() =='admin/statistics' ? ' active' : '' }}">
          <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/statistics">
          <i class="mdi mdi-chart-bar menu-icon"></i>
          <span class="menu-title">Statistics</span>
          </a>
        </li>
        @endif

          @if (auth()->user()->isAdmin() || auth()->user()->isEditor())

          <li class="nav-item{{Request::path() =='admin/users' ? ' active' : '' }}">
            <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/users">
                <i class="mdi mdi-account-multiple menu-icon"></i>
                <span class="menu-title">Users</span>
              </a>
            </li>
            @endif
          
           @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isArtistmanager() || auth()->user()->isPlaylister())
          <li class="nav-item{{Request::path() =='admin/spotifyaccounts' ? ' active' : '' }}">
            <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/spotifyaccounts">
                <i class="mdi mdi-key-plus menu-icon"></i>
                <span class="menu-title">Spotify Accounts</span>
              </a>
            </li>
            @endif

            @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
          <li class="nav-item{{Request::path() =='admin/groups' ? ' active' : '' }}">
            <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/groups">
                <i class="mdi mdi-folder-multiple menu-icon"></i>
                <span class="menu-title">Our Groups</span>
              </a>
            </li>
            @endif

            @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isPlaylister())
          <li class="nav-item{{Request::path() =='admin/spotifyplaylists' ? ' active' : '' }}">
            <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/spotifyplaylists">
                <i class="mdi mdi-playlist-play menu-icon"></i>
                <span class="menu-title">Spotify Playlists</span>
              </a>
            </li>
            @endif

            @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
            <li class="nav-item{{Request::path() =='admin/spotifytracks' ? ' active' : '' }}">
              <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/spotifytracks">
                  <i class="mdi mdi-music-note menu-icon"></i>
                  <span class="menu-title">Spotify Tracks</span>
                </a>
              </li>
              @endif

          @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
            <li class="nav-item{{ (Request::path() =='admin/searchesinprogress' || Request::path() =='admin/claimedinprogress') ? ' active' : '' }}">
              <a class="nav-link" data-toggle="collapse" href="#in-progress-menu" aria-expanded="{{ (Request::path() =='admin/searchesinprogress' || Request::path() =='admin/claimedinprogress') ? 'true' : 'false' }}" aria-controls="in-progress-menu">
                <i class="mdi mdi-cached menu-icon"></i>
                <span class="menu-title">In progress</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse{{ (Request::path() =='admin/searchesinprogress' || Request::path() =='admin/claimedinprogress') ? ' show' : '' }}" id="in-progress-menu">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link{{Request::path() =='admin/searchesinprogress' ? ' active' : '' }}" href="{{ config('myconfig.config.server_url')  }}admin/searchesinprogress">Spotify Searches</a></li>
                  <li class="nav-item"> <a class="nav-link{{Request::path() =='admin/claimedinprogress' ? ' active' : '' }}" href="{{ config('myconfig.config.server_url')  }}admin/claimedinprogress">Artist Claim Check</a></li>
                </ul>
              </div>
            </li>
            @endif

            @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAssistant())
          <li class="nav-item{{Request::path() =='admin/search' ? ' active' : '' }}">
          <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/search">
              <i class="mdi mdi-magnify menu-icon"></i>
              <span class="menu-title">Browse Spotify</span>
            </a>
          </li>
          @endif

          @if (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAssistant())
          <li class="nav-item{{Request::path() =='admin/localdatabase' ? ' active' : '' }}">
            <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/localdatabase">
              <i class="mdi mdi-database menu-icon"></i>
              <span class="menu-title">Local Database</span>
            </a>
          </li>
          @endif

          @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
          <li class="nav-item{{Request::path() =='admin/genres' ? ' active' : '' }}">
          <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/genres">
          <i class="mdi mdi-chart-bubble menu-icon"></i>
          <span class="menu-title">Genres</span>
          </a>
        </li>
        @endif

        
        @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
          <li class="nav-item{{Request::path() =='admin/tracksinfos' ? ' active' : '' }}">
          <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/tracksinfos">
          <i class="mdi mdi-library-books menu-icon"></i>
          <span class="menu-title">Track Information Widget</span>
          </a>
        </li>
        @endif

        @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
          <li class="nav-item{{Request::path() =='admin/contact' ? ' active' : '' }}">
          <a class="nav-link" href="{{ config('myconfig.config.server_url')  }}admin/contact">
          <i class="mdi mdi-email menu-icon"></i>
          <span class="menu-title">Contact (letters)</span>
          </a>
        </li>
        @endif

        

        </ul>
      </nav>


