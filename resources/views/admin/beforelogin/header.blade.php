<!DOCTYPE html>
<html lang="en">
<head>
<!--*****************************META*********************************-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>{{ $meta['title'] }}</title>
	<meta name="description" content="{{ $meta['description'] }}" />
    <meta name="keywords" content="{{ $meta['keywords'] }}" />
    <meta name="abstract" content="{{ $meta['abstract'] }}" />
    
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
  <link rel="stylesheet" href="{{ config('myconfig.config.server_url')  }}vendors/iconfonts/simple-line-icon/css/simple-line-icons.css">
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

<header>
<!-- Navigation -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      <a class="navbar-brand brand-logo" href="{{ config('myconfig.config.server_url')  }}"><img src="{{ config('myconfig.config.server_url')  }}images/logo.png" alt="logo"/></a>
      <a class="navbar-brand brand-logo-mini" href="{{ config('myconfig.config.server_url')  }}"><img src="{{ config('myconfig.config.server_url')  }}images/logo.png" alt="logo"/></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
      
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item nav-profile dropdown mr-0 mr-sm-2">
          <a class="nav-link signinlink letterspacing1" href="{{ config('myconfig.config.server_url')  }}admin/login">
            <i class="mdi mdi-login mx-0 fontsize25"></i>
            <span class="nav-profile-name">Sign in / Sign up</span>
          </a>
        </li>
      </ul>
      
    </div>
  </nav>
</header>

