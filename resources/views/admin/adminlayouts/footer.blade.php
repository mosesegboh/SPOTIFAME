

<!-- partial:partials/_footer.html -->
<footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © {{ date('Y') }} <a href="{{ config('myconfig.config.server_url')  }}" target="_blank">{{ config('myconfig.config.sitename_caps')  }}</a>. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="mdi mdi-heart text-danger"></i></span>
          </div>
        </footer>
<!-- partial -->

      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

 

<!-- inject:js -->
<script src="{{ config('myconfig.config.server_url')  }}js/off-canvas.js"></script>
  <script src="{{ config('myconfig.config.server_url')  }}js/hoverable-collapse.js"></script>
  <script src="{{ config('myconfig.config.server_url')  }}js/template.js"></script>
  <script src="{{ config('myconfig.config.server_url')  }}js/settings.js"></script>
  <script src="{{ config('myconfig.config.server_url')  }}js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{ config('myconfig.config.server_url')  }}js/dashboard.js"></script>


  <script src="{{ config('myconfig.config.server_url')  }}js/_my_page_all.js"></script>
  <script src="{{ config('myconfig.config.server_url')  }}js/_my_page_{!! str_replace('admin/', '', explode('/', Request::path())[1]) !!}.js"></script>

  <!-- End custom js for this page-->
  <script>
  
  $( document ).ready(function() {
//console.log(navigator.userAgent);

 if (navigator.userAgent.match('iPad|iPhone|iPod')) {
        $('select').addClass('uglyselect'); // provide a class for iOS select box
    }


    $('.sidenav').find('.nav-item').each(function(){
      
        var $this = $(this);
      if($this.find('.nav-link').attr('href').indexOf(location.pathname) !== -1){
            $this.removeClass('active');
        }
        
        if($this.find('.nav-link').attr('href') === base.slice(0,-1)+location.pathname.toString()){
            $this.addClass('active');
        }
    })
    
    
  })
  </script>
</body>
</html>